#アプリケーション名

模擬案件フリマアプリ

#環境構築

Docker構築

リポジトリをclone 　git clone　git@github.com:kochiyama-h/test4.git

dockerコンテナを立ち上げる docker-compose up -d

phpコンテナに移動 　docker-compose exec php bash

依存ライブラリのインストール composer install

.env ファイルを作成 cp .env.example .env

Application Keyの作成 　php artisan key:generate

マイグレーション 　php artisan migrate

シード作成 　php artisan db:seed

シンボリックリンクを作成 　php artisan storage:link

PHPunitテスト

　MySQLログイン後 　 CREATE DATABASE demo_test;

　phpコンテナ上 　　php artisan migrate --env=testing

　テスト実行 　　 ./vendor/bin/phpunit
