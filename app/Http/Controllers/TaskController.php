<?php

namespace App\Http\Controllers;

use App\Http\Requests\TaskRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TaskController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home');
    }

    public function listAll()
    {
        $tasks = auth()->user()->tasks()->get();
        return response()->json($tasks);
    }

    public function store(TaskRequest $request)
    {

        try {
            DB::beginTransaction();
            $task = auth()->user()->tasks()->create([
                'name' => $request->name,
            ]);
            DB::commit();
            return response()->json([
                'success' => true,
                'data' => $task
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Erro ao criar tarefa'
            ], 500);
        }

    }

    public function update(Request $request, $id)
    {
        try {
            DB::beginTransaction();
            $task = auth()->user()->tasks()->find($id);
            $task->update($request->all());
            DB::commit();
        }catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Erro ao atualizar tarefa'
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();
            $task = auth()->user()->tasks()->find($id);
            $task->delete();
            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Tarefa excluída com sucesso'
            ]);
        }catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Erro ao deletar tarefa'
            ], 500);
        }
    }

    public function edit($id)
    {
        $task = auth()->user()->tasks()->find($id);
        return view('edit', compact('task'));
    }

}
