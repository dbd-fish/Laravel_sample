@extends('layouts.app')

@section('content')
    <h1>タスク編集</h1>
    <form action="{{ route('tasks.update', $task->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label for="title">タイトル</label>
            <input type="text" class="form-control" id="title" name="title" value="{{ $task->title }}" required>
        </div>
        <div class="form-group">
            <label for="description">説明</label>
            <textarea class="form-control" id="description" name="description" rows="3">{{ $task->description }}</textarea>
        </div>
        <div class="form-group">
            <label for="status">状態</label>
            <select class="form-control" id="status" name="status">
                <option value="1" {{ $task->status == 1 ? 'selected' : '' }}>未着手</option>
                <option value="2" {{ $task->status == 2 ? 'selected' : '' }}>進行中</option>
                <option value="3" {{ $task->status == 3 ? 'selected' : '' }}>完了</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">更新</button>
        <a href="{{ route('tasks.index') }}" class="btn btn-secondary">キャンセル</a>
    </form>
@endsection
