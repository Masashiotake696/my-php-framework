<?php
require_once('./vendor/template.php');

// 抽象クラスを作成
abstract class BaseController {
  // 継承するコントローラーの共通処理
  abstract protected function action();

  // actionメソッドを実行
  public function executeAction() {
    $this->action();
  }
}
