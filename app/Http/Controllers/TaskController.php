<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class TaskController extends Controller
{
    public function index(Request $request) {
        $user = Auth::user();
        $tasks = $user->tasks;

        return response()->json($tasks);
    }

    public function store(Request $request) {
        $validator = Validator::make($request->all(), [
            "title" => "required|string",
            "description" => "required|string",
            "priority" => "required|in:baixa,media,alta",
            "status" => "required|in:pendente,em andamento,concluida",
        ],
        [
            "title.required" => "O titulo da tarefa é obrigatório.",
            "description.required" => "uma breve descrição é obrigatório.",
            "priority.required" => "um nivel de prioridade é obrigatória.",
            "status.required" => "é necessario passar um status para a tarefa!",
        ]);

        if ($validator->fails()) {
            return response()->json([
                "error" => $validator->errors()
            ], 400);
        }

        /** @var \App\Models\User $user */
        $user = Auth::user();

        $task = $user->tasks()->create([
            "title" => $request->title,
            "description" => $request->description,
            "priority" => $request->priority,
            "status" => $request->status,
            "user_id" => $user->id
        ]);

        return response()->json([
            "message" => "Tarefa criada com sucesso!"
        ], 201);
    }

    public function show($id) {
        $user = Auth::user();

        /** @var \App\Models\User $user */
        $task = $user->tasks()->find($id);

        if(!$task) {
            return response()->json(['message' => 'Tarefa não encontrada'], 404);
        }

        return response()->json($task);
    }

    public function update(Request $request, $id) {
        $request->validate([
            "title" => "required|string",
            "description" => "required|string",
            "priority" => "required|in:baixa,media,alta",
            "status" => "required|in:pendente,em andamento,concluida",
        ],
        [
            "title.required" => "O titulo da tarefa é obrigatório.",
            "description.required" => "uma breve descrição é obrigatório.",
            "priority.required" => "um nivel de prioridade é obrigatória.",
            "status.required" => "é necessario passar um status para a tarefa!",
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();

        $task = $user->tasks()->find($id);

        if (!$task) {
            return response()->json(['error' => 'Tarefa não encontrada.'], 404);
        }

        $task->update([
            "title" => $request->title,
            "description" => $request->description,
            "priority" => $request->priority,
            "status" => $request->status
        ]);

        return response()->json([
            "message" => "Tarefa alterada com sucesso!"
        ], 200);
    }

    public function destroy($id) {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $task = $user->tasks()->findOrFail($id);

        $task->delete();

        return response()->json(["message" => "Tarefa deletada com sucesso."]);
    }
}
