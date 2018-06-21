<?php
require_once('./vendor/Template.php');

// 抽象クラスを作成
abstract class BaseController {
  // actionメソッドを実行
  public function executeAction($action, $request) {
    $this->$action($request);
  }
}
