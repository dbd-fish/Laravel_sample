<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;

class TaskController extends Controller
{
    // タスク一覧画面を表示
    public function showIndex()
    {
        $tasks = Task::all();  // すべてのタスクを取得
        return view(view: 'tasks.index', data: compact(var_name: 'tasks'));  // ビューにデータを渡す
    }

    // タスク作成画面を表示
    public function showStore()
    {
        return view(view: 'tasks.store');  
    }

    // タスク編集画面を表示
    public function showUpdate(Task $task)
    {
        return view('tasks.update', compact(var_name: 'task'));  
    }

    // 新しいタスクの作成
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|max:255',
        ]);

        Task::create([
            'title' => $request->title,
            'description' => $request->description,
            'status' => $request->status,
        ]);

        return redirect()->route('tasks.index');
    }

    // タスクの更新
    public function update(Request $request, Task $task)
    {
        $request->validate([
            'title' => 'required|max:255',
        ]);
    
        $task->update([
            'title' => $request->title,
            'description' => $request->description,
            'status' => $request->status,
        ]);
    
        return redirect()->route('tasks.index');
    }

    // タスクの削除
    public function delete(Task $task)
    {
        $task->delete();

        return redirect()->route('tasks.index');
    }
}
