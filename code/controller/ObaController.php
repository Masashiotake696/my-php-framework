<?php
require_once('./vendor/routes.php');

class ObaController extends BaseController {
  public function action() {
    // ビューを取得
    $view = $this->getView();
    // テンプレートをセット
    $view->setTemplate('oba');
    // 埋め込む値をセット
    $view->setTemplateKeyValue('obavar', Routes::getOneQuery('obavar'));
    // 出力
    $view->outputTemplate();
  }
}
