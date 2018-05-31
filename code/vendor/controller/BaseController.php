<?php
require_once('./vendor/template.php');

// 抽象クラスを作成
abstract class BaseController {
  protected $db;

  // DB接続コンストラクタ
  public function __construct() {
    require_once('./vendor/env.php');
    // DB接続
    $this->db = new PDO('mysql:host=mysql;dbname=' . $DB_DATABASE, $DB_USERNAME, $DB_PASSWORD);

    // トランザクションの開始
    $this->db->beginTransaction();
  }

  public function __destruct() {
    try {
      if($this->db->errorCode() !== "00000") {
        throw new PDOException('Errorあるよ');
      }
      // 例外処理がなかった場合はコミット
      $this->db->commit();
      echo 'できたよ';
    } catch(PDOException $e) {
      // 接続に失敗した場合はエラーメッセージを出す
      $this->db->rollback();
      echo  $e->getMessage();
    }
  }

  // 継承するコントローラーの共通処理
  abstract public function action();
}
