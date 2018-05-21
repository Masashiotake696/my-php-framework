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
    if($this->has_query($request)) {
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

  public function getQuery($param) {
    // クエリを取得
    $request = $this->getRequest(1);

    // クエリがあるか判定
    if($this->has_query($request)) {
      // 「?」以降を取得
      $query = ltrim(strstr($request, '?'), '?');
      // 「&」で区切る
      $array = explode('&', $query);

      // クエリをkeyとvalueの形で配列に格納
      $queryArray = array();
      foreach ($array as $value) {
        // 「=」で区切る
        $buf = explode('=', $value, 2);
        $queryArray[$buf[0]] = $buf[1];
      }

      // 引数で指定されたkeyが存在する場合にそれを返す
      if(is_null($queryArray[$param])) {
        return '';
      } else {
        return $queryArray[$param];
      }
    } else {
      return '';
    }
  }

  /*
  * コントローラーファイルが存在するかを判定
  * @param String リクエストを除いたクエリ
  * @return bool
  */
  private function existsController($path) {
    if(is_null($path)) {
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

  private function has_query($request) {
    // リクエストが「?」と「=」を含むかを判定
    if(strpos($request, '?') !== FALSE && strpos($request, '=') !== FALSE) {
      return TRUE;
    } else {
      return FALSE;
    }
  }
}
