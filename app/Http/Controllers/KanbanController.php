<?php

namespace App\Http\Controllers;

use App\Models\KanbanCard;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class KanbanController extends Controller
{
    const COLUMNS = ['backlog', 'todo', 'undone', 'on_progress', 'done', 'archive'];

    // ── Pages ────────────────────────────────────────────

    public function index()
    {
        $raw = KanbanCard::with(['assignedUser:id,name', 'creator:id,name'])
            ->orderBy('position')
            ->get()
            ->map(fn($c) => $this->formatCard($c, true))
            ->groupBy('column_name');

        // Ensure every column exists (even if empty)
        $cardsData = collect(self::COLUMNS)
            ->mapWithKeys(fn($col) => [$col => ($raw->get($col) ?? collect())->values()]);

        $users = User::orderBy('name')->get(['id', 'name', 'email']);

        return view('kanban.index', compact('cardsData', 'users'));
    }

    // ── API ──────────────────────────────────────────────

    /**
     * Return single card (for edit / detail fetch).
     */
    public function show(KanbanCard $card): JsonResponse
    {
        $card->load(['assignedUser:id,name', 'creator:id,name']);

        return response()->json($this->formatCard($card, true));
    }

    /**
     * Create card — admin only.
     */
    public function store(Request $request): JsonResponse
    {
        if (! Auth::user()->isAdmin()) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        $data = $request->validate([
            'title'       => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'column_name' => ['required', Rule::in(self::COLUMNS)],
            'color'       => ['nullable', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'priority'    => ['nullable', Rule::in(['low', 'medium', 'high'])],
            'due_date'    => ['nullable', 'date'],
            'assigned_to' => ['nullable', 'exists:users,id'],
        ]);

        $position = KanbanCard::where('column_name', $data['column_name'])->count();

        $card = KanbanCard::create([
            ...$data,
            'position'   => $position,
            'created_by' => Auth::id(),
        ]);

        $card->load(['assignedUser:id,name', 'creator:id,name']);

        return response()->json([
            'message' => 'Card created.',
            'card'    => $this->formatCard($card, true),
        ], 201);
    }

    /**
     * Update card — admin only.
     */
    public function update(Request $request, KanbanCard $card): JsonResponse
    {
        if (! Auth::user()->isAdmin()) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        $data = $request->validate([
            'title'       => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'column_name' => ['required', Rule::in(self::COLUMNS)],
            'color'       => ['nullable', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'priority'    => ['nullable', Rule::in(['low', 'medium', 'high'])],
            'due_date'    => ['nullable', 'date'],
            'assigned_to' => ['nullable', 'exists:users,id'],
        ]);

        $card->update($data);
        $card->load(['assignedUser:id,name', 'creator:id,name']);

        return response()->json([
            'message' => 'Card updated.',
            'card'    => $this->formatCard($card, true),
        ]);
    }

    /**
     * Delete card — admin only.
     */
    public function destroy(KanbanCard $card): JsonResponse
    {
        if (! Auth::user()->isAdmin()) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        $card->delete();

        return response()->json(['message' => 'Card deleted.']);
    }

    /**
     * Save column order after drag — all authenticated users.
     * Body: { "columns": { "backlog": [1,3,2], "todo": [4,5], ... } }
     */
    public function reorder(Request $request): JsonResponse
    {
        $data = $request->validate([
            'columns'     => ['required', 'array'],
            'columns.*'   => ['array'],
            'columns.*.*' => ['integer'],
        ]);

        foreach ($data['columns'] as $column => $cardIds) {
            if (! in_array($column, self::COLUMNS)) {
                continue;
            }
            foreach ($cardIds as $position => $id) {
                KanbanCard::where('id', $id)->update([
                    'column_name' => $column,
                    'position'    => $position,
                ]);
            }
        }

        return response()->json(['message' => 'Board saved.']);
    }

    // ── Internal ─────────────────────────────────────────

    private function formatCard(KanbanCard $card, bool $full = false): array
    {
        $result = [
            'id'            => $card->id,
            'title'         => $card->title,
            'column_name'   => $card->column_name,
            'position'      => $card->position,
            'color'         => $card->color ?? '#696cff',
            'priority'      => $card->priority,
            'due_date'      => $card->due_date?->format('Y-m-d'),
            'due_date_fmt'  => $card->due_date?->format('d M Y'),
            'assigned_to'   => $card->assigned_to,
            'assigned_name' => $card->assignedUser?->name,
            'created_by'    => $card->created_by,
            'creator_name'  => $card->creator?->name,
        ];

        if ($full) {
            $result['description'] = $card->description;
        }

        return $result;
    }
}
