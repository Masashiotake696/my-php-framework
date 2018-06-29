<?php
require_once('./model/Users.php');

class ObachanController extends DatabaseController {
  /**
   * ページを表示
   */
  public function show() {
    $all_user = Users::all();
    var_dump($all_user);
    $find_user = Users::find(7);
    var_dump($find_user);

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
  }
}
