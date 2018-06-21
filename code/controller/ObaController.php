<?php

class ObaController extends BaseController {
  public function show(Request $request) {
    // テンプレートインスタンスの生成
    $oba = new Template('oba');
    // 埋め込む値をセット
    $oba->setKeyValue('obavar', $request->getOneQuery('obavar'));
    // 出力
    $oba->outputTemplate();
  }
}
