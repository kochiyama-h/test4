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
