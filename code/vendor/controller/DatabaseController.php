<?php

abstract class DatabaseController extends BaseController {
    protected $db;

    // コンストラクタ
    public function __construct() {
        // DB接続
        $this->connectDbMysql();
    }

    // トランザクションを実行してからactionを実行
    public function executeAction($action, $request) {
        try {
            // トランザクションの開始
            $this->db->beginTransaction();
            // アクションを実行
            $this->$action($request);
            // 例外処理がなかった場合はコミット
            $this->db->commit();
            echo 'できたよ';
        } catch(Exception $e) {
            // 接続に失敗した場合はエラーメッセージを出す
            $this->db->rollback();

            // 
            header("HTTP/1.0 500 Internal Server Error");
            echo(file_get_contents("./view/500.html"));
            exit;
        }
    }

    // MysqlDBに接続
    private function connectDbMysql() {
        // .envファイルを読み込む
        require_once('./vendor/env.php');
        // DB接続
        $this->db = new PDO('mysql:host=mysql;dbname=' . $DB_DATABASE, $DB_USERNAME, $DB_PASSWORD);
        // データベース接続後にエラーが起きたら例外を投げるオプションを指定
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
}
