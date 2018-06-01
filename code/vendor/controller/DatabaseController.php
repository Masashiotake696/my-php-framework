<?php
require_once('./vendor/controller/BaseController.php');

abstract class DatabaseController {
    protected $db;

    // 継承するコントローラーの共通処理
    protected abstract function action();

    // コンストラクタ
    public function __construct() {
        // DB接続
        $this->connectDbMysql();
    }

    // トランザクションを実行してからactionを実行
    public function executeAction() {
        try {
            // トランザクションの開始
            $this->db->beginTransaction();
            // アクションを実行
            $this->action();
            // 例外処理がなかった場合はコミット
            $this->db->commit();
            echo 'できたよ';
        } catch(Exception $e) {
            // 接続に失敗した場合はエラーメッセージを出す
            $this->db->rollback();
            echo  'Error: ' . $e->getMessage();
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