<?php
class Routes {
  /*
  * URLに応じた処理を行うコントローラー名を返す
  * @return String コントローラー名
  */
  public static function getControllerName() {
    // リクエストを取得
    $request = Routes::getRequest(1);

    // リクエストのクエリを削除
    if(Routes::has_query($request)) {
      $request = strstr($request, '?', true);
    }

    // リクエストの最初の文字を大文字にする
    $request = ucfirst($request);

    // ファイルが存在するか確かめる
    if(Routes::existsController($request)) {
      // 文字列整形して返す
      return $request . 'Controller';
    } else {
      return 'OthersController';
    }
  }

  /*
  * 全てのクエリをキーバリューで取得する
  * @return array 全てのクエリをキーバリューで格納した配列
  */
  public static function getAllQuery() {
    // クエリを取得
    $request = Routes::getRequest(1);

    // クエリがあるか判定
    if(Routes::has_query($request)) {
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
      return $queryArray;
    } else {
      return [];
    }
  }

  /*
  * 特定のクエリをキーバリューで取得する
  * @param $queryName クエリ名
  * @return array 引数で指定したクエリをキーバリューで格納した配列
  */
  public static function getOneQuery($queryName) {
    $array = Routes::getAllQuery();
    if(count($array) > 0) {
      foreach ($array as $key => $value) {
        if($key === $queryName) {
          return $value;
        }
      }
    }
    return '';
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

  /*
  * リクエストにクエリがあるか判定
  * @param リクエスト
  * @return bool
  */
  private function has_query($request) {
    // リクエストが「?」と「=」を含むかを判定
    if(strpos($request, '?') !== FALSE && strpos($request, '=') !== FALSE) {
      return TRUE;
    } else {
      return FALSE;
    }
  }
}
