## 1\. 環境設定

### 1.1 Dockerのインストール

- Docker Desktopをダウンロード: https://www.docker.com/products/docker-desktop
- インストーラーを実行し、指示に従ってインストール
- WSL 2のインストールが求められた場合は、指示に従ってインストール
- インストール完了後、Docker Desktopを起動

### 1.2 プロジェクトの準備

- コマンドプロンプトを開き、プロジェクトを作成したいディレクトリに移動
    
- 以下のコマンドを実行してプロジェクトディレトリを作成:
    
    ```
    mkdir todo-list
    cd todo-list
    ```
    

### 1.3 Dockerファイルの作成

- プロジェクトルートに「docker-compose.yml」ファイルを作成し、以下の内容を追加:
    
    ```yaml
    version: '3'
    services:
      app:
        build:
          context: .
          dockerfile: Dockerfile
        ports:
          - "8000:8000"
        volumes:
          - .:/app
        depends_on:
          - db
      db:
        image: mysql:5.7
        environment:
          MYSQL_DATABASE: todo_list
          MYSQL_ROOT_PASSWORD: root_password
          MYSQL_USER: user
          MYSQL_PASSWORD: password
        volumes:
          - dbdata:/var/lib/mysql
    volumes:
      dbdata:
    ```
    
- 同じディレクトリに「Dockerfile」を作成し、以下の内容を追加:
    
    ```jsx
        FROM php:8.1-fpm

        RUN apt-get update && apt-get install -y \
            git \
            curl \
            libpng-dev \
            libonig-dev \
            libxml2-dev \
            zip \
            unzip

        RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

        COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

        # 作業ディレクトリを作成
        WORKDIR /var/www/app

        # プロジェクトファイルをコンテナにコピー
        COPY . .

        # 環境変数設定：Composerをrootで実行できるようにする
        ENV COMPOSER_ALLOW_SUPERUSER=1

        # 既存のcomposer.jsonが存在するか確認し、存在しない場合はプロジェクトを作成
        # 存在している場合はcomposer installを実行して依存関係をインストール
        RUN if [ ! -f composer.json ]; then \
            rm -rf * && \
            composer create-project --prefer-dist laravel/laravel .; \
            else composer install; \
            fi

        # Laravel開発サーバーの起動
        CMD php artisan serve --host=0.0.0.0 --port=8000


    ```
    

## 2.1 Laravelプロジェクトの作成

`docker-compose up -d`でビルド＆立ち上げ。立ち上げ時にコンテナ内でLaravelプロジェクトが作成される。

`docker cp <コンテナ名>:/var/www/app ./app`でコンテナ内にしかないLaravelプロジェクトをWindows11環境にもコピー
> 例：　docker cp todo-list-app-1:/var/www/app ./app

DockerfileのVolumeをコメントアウトして再度`docker-compose up -d`




### 2.2 .envファイルの設定

- .envファイルを開き、以下のように編集:
    
    ```
    DB_CONNECTION=mysql
    DB_HOST=db
    DB_PORT=3306
    DB_DATABASE=todo_list
    DB_USERNAME=user
    DB_PASSWORD=password
    ```
    

## 3\. Dockerコンテナの起動

- 以下のコマンドを実行してDockerコンテナを起動:
    
    ```
    docker-compose up -d
    ```
    

## 4\. データベースのセットアップ

- 以下のコマンドを実行してマイグレーションを実行:
    
    ```
    docker-compose exec app php artisan migrate
    ```
    

## 5\. モデルとマイグレーションの作成

- 以下のコマンドを実行してTaskモデルとマイグレーションを作成:
    
    ```
    docker-compose exec app php artisan make:model Task -m
    ```
    
- database/migrations/xxxx_xx_xx_xxxxxx_create_tasks_table.phpを開き、以下のように編集:
    
    ```php
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

    ```
    
- app\app\Models\Task.phpを作成:

    ```php
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
    ```

- マイグレーションを実行:
    
    ```
    docker-compose exec app php artisan migrate
    ```

- マイグレーションを実行(既存テーブルを全て削除する場合):
    ```
    docker-compose exec app php artisan migrate:fresh
    ```
    


## 6\. コントローラーの作成

- 以下のコマンドを実行してTaskControllerを作成:
    
    ```
    docker-compose exec app php artisan make:controller TaskController --resource
    ```

- app\app\Http\Controllers\TaskController.phpを開き、以下のように編集:

    ```php
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


    ```


## 7\. ルーティングの設定

- routes/web.phpを開き、以下のように編集:
    
    ```php
    <?php

    use Illuminate\Support\Facades\Route;
    use App\Http\Controllers\TaskController;


    // タスク管理用のルート
    Route::prefix('tasks')->name('tasks.')->controller(TaskController::class)->group(function () {
        Route::get('/index', 'showIndex')->name('index');
        Route::get('/store', 'showStore')->name('store');
        Route::post('/store', 'store')->name('store');
        Route::get('/update/{task}', 'showUpdate')->name('update');
        Route::put('/update/{task}', 'update')->name('update');
        Route::delete('/delete/{task}', 'delete')->name('delete');
    });
    ```
    

## 8\. ビューの作成

- 共通レイアウトの作成
    -app\resources\views\layouts\app.blade.phpディレクトリを作成し、以下のファイルを作成します：
        ```php
            <!DOCTYPE html>
            <html lang="ja">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>タスク管理システム</title>
                <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
            </head>
            <body class="bg-light">
                <!-- ヘッダー部分 -->
                <header class="bg-dark text-white py-4 mb-4">
                    <div class="container text-center">
                        <h1 class="display-4">タスク管理システム</h1>
                    </div>
                </header>

                <!-- メインコンテンツ部分 -->
                <main class="container">
                    <div class="bg-white p-4 rounded shadow-sm">
                        @yield('content')
                    </div>
                </main>
            </body>
            </html>

        ```

- ビューファイルの作成
    
    -app\resources\views\tasksディレクトリを作成し、以下のファイルを作成します：
        
        - index.blade.php（タスク一覧画面）
            ```php
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
            ```


        - store.blade.php（タスク作成画面）

            ```php
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

            ```


        - update.blade.php（タスク編集画面）

            ```php
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

            ```


これらのファイルは、HTMLとLaravelのBladeテンプレート構文を使用して、フォームと一覧表示を実装しています。各ファイルは@extendsディレクティブを使用してレイアウトを継承し、@sectionディレクティブを使用してコンテンツを定義しています。

## 9\. アプリケーションの動作確認

- ブラウザで`http://localhost:8000/tasks/index`にアクセスし、TODOリストアプリケーションが正常に動作することを確認

## 10\. 開発とデバッグ

- コードの変更は自動的にDockerコンテナに反映されます
    
- ログの確認:
    
    ```
    docker-compose logs app
    ```

## 11\. Dockerコンテナの停止

- 開発終了時、以下のコマンドでDockerコンテナを停止:
    
    ```
    docker-compose down
    ```
    
