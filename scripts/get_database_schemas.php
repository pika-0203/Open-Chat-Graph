<?php

// DB接続設定を読み込み
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../local-secrets.php';

use App\Models\Repositories\DB;

// MySQL接続
DB::connect();

echo "=== MySQL Database Schema ===\n\n";

// すべてのテーブルを取得
$tables = DB::$pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);

foreach ($tables as $table) {
    echo "-- Table: $table\n";
    $schema = DB::$pdo->query("SHOW CREATE TABLE `$table`")->fetch(PDO::FETCH_ASSOC);
    echo $schema['Create Table'] . ";\n\n";
}

echo "\n=== SQLite Database Schemas ===\n\n";

// SQLiteデータベースファイルのパス
$sqliteDbs = [
    'statistics' => __DIR__ . '/../storage/ja/SQLite/statistics/statistics.db',
    'ranking_position' => __DIR__ . '/../storage/ja/SQLite/ranking_position/ranking_position.db'
];

foreach ($sqliteDbs as $name => $path) {
    if (file_exists($path)) {
        echo "-- SQLite Database: $name\n";
        echo "-- Path: $path\n\n";
        
        $sqlite = new PDO("sqlite:$path");
        $sqlite->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // すべてのテーブルを取得
        $tables = $sqlite->query("SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%'")->fetchAll(PDO::FETCH_COLUMN);
        
        foreach ($tables as $table) {
            echo "-- Table: $table\n";
            $schema = $sqlite->query("SELECT sql FROM sqlite_master WHERE type='table' AND name='$table'")->fetch(PDO::FETCH_COLUMN);
            echo $schema . ";\n\n";
        }
        
        $sqlite = null;
    }
}