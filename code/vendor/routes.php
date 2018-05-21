<?php
class Route {
  /*
  * URLに応じた処理を行うコントローラー名を返す
  * @return String コントローラー名
  */
  public function getControllerName() {
    // リクエストを取得
    $request = $this->getRequest(1);

    // リクエストのクエリを削除
    if(strpos($request, '?') !== FALSE) {
      $request = strstr($request, '?', true);
    }

    // リクエストの最初の文字を大文字にする
    $request = ucfirst($request);

    // ファイルが存在するか確かめる
    if($this->existsController($request)) {
      // 文字列整形して返す
      return $request . 'Controller';
    } else {
      return 'OthersController';
    }
  }

  public function getQuery() {
    // クエリを取得
    $query = $this->getRequest(2);

    if(is_null($query)) { // クエリがNULLか判定

    } else {
      // 「=」以降を取得
      preg_match('/^\?(.+)=(.+)$/', $query, $result);
    }
  }

  /*
  * コントローラーファイルが存在するかを判定
  * @param String リクエストを除いたクエリ
  * @return bool
  */
  private function existsController($path) {
    if($path === NULL) {
      return FALSE;
    } else {
      // パスを作成
      $path = $path . 'Controller.php';
      
      // ファイルが存在するか判定
      return file_exists('./controller/' . $path);
    }
  }

  /*
  * URL階層の内、subscript番目(1からスタート)の階層を返す
  * @param int $subscript 階層番号
  * @return string リクエスト
  */
  private function getRequest($subscript) {
    // リクエストを取得
    $requestUrl = $_SERVER['REQUEST_URI'];
    // 先頭と末尾の「/」を削除
    $requestUrl = rtrim($requestUrl, '/');

    // 文字列を「/」で分割
    $params = explode('/', $requestUrl);

    // 引数に応じてリクエストを返す
    return $params[$subscript];
  }
}
