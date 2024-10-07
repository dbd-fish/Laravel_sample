@extends('layouts.app')

@section('content')
    <h1>新規タスク作成</h1>
    <form action="{{ route('tasks.store') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="title">タイトル</label>
            <input type="text" class="form-control" id="title" name="title" required>
        </div>
        <div class="form-group">
            <label for="description">説明</label>
            <textarea class="form-control" id="description" name="description" rows="3"></textarea>
        </div>
        <div class="form-group">
            <label for="status">状態</label>
            <select class="form-control" id="status" name="status">
                <option value="1">未着手</option>
                <option value="2">進行中</option>
                <option value="3">完了</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">作成</button>
        <a href="{{ route('tasks.index') }}" class="btn btn-secondary">戻る</a>
    </form>
@endsection
