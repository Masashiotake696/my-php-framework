<?php
require_once('./vendor/Model.php');

abstract class DatabaseController extends BaseController {
    private static $pdo;

    public function __construct() {
        $this->connectDbMysql();
    }

    // トランザクションを実行してからactionを実行
    public function executeAction($action, $request) {
        try {
            // トランザクションの開始
            self::$pdo->beginTransaction();
            // アクションを実行
            $this->$action($request);
            // 例外処理がなかった場合はコミット
            self::$pdo->commit();
        } catch(Exception $e) {
            // 接続に失敗した場合はエラーメッセージを出す
            self::$pdo->rollback();

            echo $e->getMessage();

            // 500エラーを返す
            header("HTTP/1.0 500 Internal Server Error");
            echo(file_get_contents("./view/500.html"));
            exit;
        }
    }

    // MysqlDBに接続
    private function connectDbMysql() {
        // .envファイルを読み込む
        require_once('./vendor/env.php');
        // すでに値がセットされていた場合は
        if (!isset(self::$pdo)) {
            self::$pdo = new PDO('mysql:host=mysql;dbname=' . $DB_DATABASE, $DB_USERNAME, $DB_PASSWORD);
            // データベース接続後にエラーが起きたら例外を投げるように指定
            self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            // フェッチスタイルをカラム名をキーとする連想配列で取得するように設定
            self::$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        }
    }

    public static function getPdo() {
        return self::$pdo;
    }
}
