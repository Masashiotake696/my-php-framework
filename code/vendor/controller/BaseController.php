<?php
require_once('./vendor/template.php');

// 抽象クラスを作成
abstract class BaseController {
  private $view;

  // コンストラクタでビューをセット
  public function __construct() {
    $this->view = new Template();
  }

  // ビューを返す
  public function getView() {
    return $this->view;
  }

  // 継承するコントローラーの共通処理
  abstract public function action();
}
