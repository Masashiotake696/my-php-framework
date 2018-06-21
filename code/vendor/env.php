<?php
// .envファイルを開く
//$envFile = fopen('.env', 'r');
$envFile = file_get_contents('.env');
$env_array = explode("\n", $envFile);

$DB_DATABASE = '';
$DB_USERNAME = '';
$DB_PASSWORD = '';

// .envフィルが存在するか判定
if($envFile) {
    foreach($env_array as $line) {
        // 空白を削除
        $line = preg_replace('/\s/', '', $line);
        // 文字列を「=」で区切る
        $line_array = explode("=", $line);

        if(count($line_array) === 2) {
            switch ($line_array[0]) {
                case 'DB_DATABASE':
                    $DB_DATABASE = $line_array[1];
                    break;
                case 'DB_USERNAME':
                    $DB_USERNAME = $line_array[1];
                    break;
                case 'DB_PASSWORD':
                    $DB_PASSWORD = $line_array[1];
                    break;
                default:
                    break;
            }
        }
    }
}
