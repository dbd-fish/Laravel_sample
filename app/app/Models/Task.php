<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    // テーブルに対応するモデルが操作可能なカラムを指定
    protected $fillable = [
        'title',        // タスクのタイトル
        'description',  // タスクの詳細
        'status',       // タスクのステータス（1=未着手, 2=進行中, 3=完了）
    ];

    // ステータスをラベルに変換するアクセサ
    public function getStatusLabelAttribute()
    {
        return match ($this->status) {
            1 => '未着手',
            2 => '進行中',
            3 => '完了',
            default => '不明',
        };
    }
}
