<?php

class ObachanController extends BaseController {
  /**
   * ページを表示
   */
  public function show() {
    // テンプレートインスタンスの生成
    $obachan = new Template('obachan');
    // 埋め込む値をセット
    // $oba->setTemplateKeyValue('obavar', Routes::getOneQuery('obavar'));
    // 出力
    $obachan->outputTemplate();
  }

  /**
   * 子リソースの作成
   */
  public function create($request) {
    $request->validate([
      'name' => ['string', 'min:1', 'max:20'],
      'age' => ['numeric', 'min:0', 'max:130'],
      'address' => ['string', 'require'],
    ]);

    
  }
}
