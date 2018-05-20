<?php
class Route {
  // URLに応じた処理を行うコントローラー名を返す
  public function getControllerName() {
    // リクエストを取得
    $requestUrl = $_SERVER['REQUEST_URI'];
    // リクエストの取得
    preg_match('/^\/(.+)(\?.*)?$/', $requestUrl, $request);
    // ファイルが存在するか確かめる
    if($this->existsController($request[1])) {
      // 文字列整形して返す
      return ucfirst($request[1]) . 'Controller';
    } else {
      return 'OthersController';
    }
  }

  private function existsController($path) {
    // パスを作成
    $path = ucfirst($path) . 'Controller.php';
    return file_exists('../controller/' . $path);
  }
}
