<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * マイグレーションの実行時にテーブルを作成します
     *
     * @return void
     */
    public function up()
    {
        // tasksテーブルを作成
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();  // 自動インクリメントID
            $table->string('title');  // タスクのタイトル
            $table->text('description')->nullable();  // タスクの詳細（null許容）
            $table->integer('status')->default(value: 1);  // タスクのステータス（1=未着手, 2=進行中, 3=完了）
            $table->timestamps();  // 作成日時、更新日時を自動的に管理
        });
    }

    /**
     * Reverse the migrations.
     * マイグレーションのロールバック時にテーブルを削除します
     *
     * @return void
     */
    public function down()
    {
        // tasksテーブルを削除
        Schema::dropIfExists('tasks');
    }
};
