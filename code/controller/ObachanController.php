<?php

class ObachanController extends BaseController {
  /**
   * ページを表示
   */
  public function show() {
    // テンプレートインスタンスの生成
    $obachan = new Template('obachan');

    // ビューの表示
    $obachan->outputTemplate();
  }

  /**
   * とりあえずバリデーションする
   */
  public function create(Request $request) {
    $request->validate(
      [
        'name' => ['string', 'min:1', 'max:20'],
        'age' => ['number', 'require', 'min:0', 'max:130'],
        'address' => ['string', 'require'],
      ],
      'obachan'
    );

    echo 'OK';
    exit;
  }
}
