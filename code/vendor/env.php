<?php
// .envファイルを開く
$envFile = fopen('.env', 'r');

$DB_DATABASE = '';
$DB_USERNAME = '';
$DB_PASSWORD = '';

// .envフィルが存在するか判定
if($envFile) {
    // 一行ずつ読み込み
    while($line = fgets($envFile)) {
        // 空白を削除
        $line = preg_replace('/\s/', '', $line);
        // 文字列を「=」で区切る
        $array = explode("=", $line);
        if(count($array) === 2) {
            switch ($array[0]) {
                case 'DB_DATABASE':
                    $DB_DATABASE = $array[1];
                    break;
                case 'DB_USERNAME':
                    $DB_USERNAME = $array[1];
                    break;
                case 'DB_PASSWORD':
                    $DB_PASSWORD = $array[1];
                    break;
                default:
                    break;
            }
        }
    }
}