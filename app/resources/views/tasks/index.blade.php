@extends('layouts.app')

@section('content')
    <h1>タスク一覧</h1>
    <a href="{{ route('tasks.store') }}" class="btn btn-primary mb-3">新規タスク作成</a>
    
    @if($tasks->count() > 0)
        <table class="table">
            <thead>
                <tr>
                    <th>タイトル</th>
                    <th>状態</th>
                    <th>操作</th>
                </tr>
            </thead>
            <tbody>
                @foreach($tasks as $task)
                    <tr>
                        <td>{{ $task->title }}</td>
                        <!-- statusはアクセサを使用して変換された値を表示 -->
                        <td>{{ $task->status_label }}</td>
                        <td>
                            <a href="{{ route('tasks.update', $task->id) }}" class="btn btn-sm btn-warning">編集</a>
                            <form action="{{ route('tasks.delete', $task->id) }}" method="POST" style="display: inline-block;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('本当に削除しますか？')">削除</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p>タスクはありません。</p>
    @endif
@endsection
