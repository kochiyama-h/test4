#アプリケーション名

模擬案件勤怠アプリ

#環境構築

Docker構築

リポジトリをclone 　git clone　git@github.com:kochiyama-h/test4.git

dockerコンテナを立ち上げる docker-compose up -d

phpコンテナに移動 　docker-compose exec php bash

依存ライブラリのインストール composer install

「.env.example」ファイルを 「.env」ファイルに命名を変更。

.envに以下の環境変数を追加

DB_CONNECTION=mysql

DB_HOST=mysql

DB_PORT=3306

DB_DATABASE=laravel_db

DB_USERNAME=laravel_user

DB_PASSWORD=laravel_pass


Application Keyの作成 　php artisan key:generate

マイグレーション 　php artisan migrate

シード作成 　php artisan db:seed

シンボリックリンクを作成 　php artisan storage:link

PHPunitテスト

　MySQLログイン後 　 CREATE DATABASE demo_test;

　phpコンテナ上 　　php artisan migrate --env=testing

　テスト実行 　　 ./vendor/bin/phpunit


er図

erDiagram
    USERS ||--o{ ATTENDANCE_RECORDS : has
    USERS ||--o{ MODIFICATION_REQUESTS : makes

    ATTENDANCE_RECORDS }o--|| USERS : belongs_to
    ATTENDANCE_RECORDS ||--o{ BREAKS : has
    ATTENDANCE_RECORDS ||--o{ MODIFICATION_REQUESTS : has

    BREAKS }o--|| ATTENDANCE_RECORDS : belongs_to
    BREAKS ||--o{ MODIFICATION_REQUESTS : has

    MODIFICATION_REQUESTS }o--|| USERS : belongs_to
    MODIFICATION_REQUESTS }o--|| ATTENDANCE_RECORDS : belongs_to
    MODIFICATION_REQUESTS }o--|| BREAKS : targets

    USERS {
        int id PK
        string name
        string email
        string password
        boolean is_admin
        timestamp created_at
        timestamp updated_at
    }

    ATTENDANCE_RECORDS {
        int id PK
        int user_id FK
        date date
        time clock_in
        time clock_out
        string status
        string reason
        timestamp created_at
        timestamp updated_at
    }

    BREAKS {
        int id PK
        int attendance_record_id FK
        time start_time
        time end_time
        timestamp created_at
        timestamp updated_at
    }

    MODIFICATION_REQUESTS {
        int id PK
        int user_id FK
        int attendance_record_id FK
        int target_break_id FK
        date before_date
        date after_date
        time before_clock_in
        time after_clock_in
        time before_clock_out
        time after_clock_out
        time before_break_start
        time after_break_start
        time before_break_end
        time after_break_end
        string reason
        string status
        timestamp created_at
        timestamp updated_at
    }
