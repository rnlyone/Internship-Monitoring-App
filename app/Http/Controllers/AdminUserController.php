<?php

namespace App\Http\Controllers;

use App\Models\ScheduleSlot;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class AdminUserController extends Controller
{
    /**
     * Show the user management page.
     */
    public function index()
    {
        $totalUsers  = User::count();
        $totalAdmins = User::where('role', 'admin')->count();
        $totalInterns = User::where('role', 'intern')->count();

        return view('admin.users', compact('totalUsers', 'totalAdmins', 'totalInterns'));
    }

    /**
     * Return paginated user list as JSON.
     * ?role=&search=&page=
     */
    public function list(Request $request): JsonResponse
    {
        $query = User::query();

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }
        if ($request->filled('search')) {
            $s = '%' . $request->search . '%';
            $query->where(fn($q) => $q->where('name', 'like', $s)->orWhere('email', 'like', $s));
        }

        $users = $query->orderBy('name')->get()->map(fn($u) => [
            'id'              => $u->id,
            'name'            => $u->name,
            'email'           => $u->email,
            'role'            => $u->role,
            'created_at'      => $u->created_at->format('d M Y'),
            'schedule_count'  => $u->scheduleSlots()->count(),
            'is_self'         => $u->id === Auth::id(),
        ]);

        return response()->json(['users' => $users, 'total' => $users->count()]);
    }

    /**
     * Store a new user.
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'unique:users,email'],
            'password' => ['required', Password::min(8)],
            'role'     => ['required', Rule::in(['admin', 'intern'])],
        ]);

        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
            'role'     => $data['role'],
        ]);

        return response()->json([
            'message' => 'User created successfully.',
            'user'    => [
                'id'             => $user->id,
                'name'           => $user->name,
                'email'          => $user->email,
                'role'           => $user->role,
                'created_at'     => $user->created_at->format('d M Y'),
                'schedule_count' => 0,
                'is_self'        => false,
            ],
        ], 201);
    }

    /**
     * Return single user data for edit form.
     */
    public function show(User $user): JsonResponse
    {
        return response()->json([
            'id'    => $user->id,
            'name'  => $user->name,
            'email' => $user->email,
            'role'  => $user->role,
        ]);
    }

    /**
     * Update an existing user.
     */
    public function update(Request $request, User $user): JsonResponse
    {
        $data = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => ['nullable', Password::min(8)],
            'role'     => ['required', Rule::in(['admin', 'intern'])],
        ]);

        $user->name  = $data['name'];
        $user->email = $data['email'];
        $user->role  = $data['role'];
        if (!empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }
        $user->save();

        return response()->json(['message' => 'User updated successfully.']);
    }

    /**
     * Delete a user (cannot delete yourself).
     */
    public function destroy(User $user): JsonResponse
    {
        if ($user->id === Auth::id()) {
            return response()->json(['message' => 'You cannot delete your own account.'], 422);
        }

        // Cascade-delete related schedules
        ScheduleSlot::where('user_id', $user->id)->each(function ($slot) {
            $slot->presenceStamps()->delete();
            $slot->shiftLogbooks()->delete();
            $slot->delete();
        });

        $user->delete();

        return response()->json(['message' => 'User deleted successfully.']);
    }
}
