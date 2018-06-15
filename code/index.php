<?php
// vendorファイルの読み込み
require_once('./vendor/request.php');
require_once('./vendor/controller/BaseController.php');
require_once('./vendor/controller/DatabaseController.php');

// コントローラーのファイルを読み込み
spl_autoload_register(function($className) {
  if(strpos($className, 'Controller')) {
    require_once('controller/' . $className . '.php');
  }
});


try {
  // HTTPメソッドを取得
  $method = $_SERVER['REQUEST_METHOD'];

  // クエリを取得
  if($method !== 'GET') { // HTTPメソッドがGET以外の場合はHTTPリクエストのボディからクエリを取得
    $query = file_get_contents('php://input');
  } else {
    $query = $_SERVER['QUERY_STRING'];
  }

  // クエリが空であるか判定
  if(!empty($query)) {
    // &で区切る
    $query_array = explode('&', $query);
    // 連想配列の形でクエリを格納
    $query_hash = [];
    foreach($query_array as $query) {
      if(strpos($query, '=') !== FALSE) { // &で区切ったクエリが'='を含むか判定
        $query = explode('=', $query);
        if(count($query) === 2) { // =で分割したクエリがX=Yの形になっているか判定
          $query_hash[$query[0]] = $query[1];
        }
      }
    }

    // リクエストインスタンスを生成
    $request = new Request($query_hash);
  }

  // パスを取得
  if(strpos($_SERVER['REQUEST_URI'], '?') !== FALSE) { // URIにクエリを含むか判定
    $path = strstr($_SERVER['REQUEST_URI'], '?', true);
  } else {
    $path = $_SERVER['REQUEST_URI'];
  }

  // ルーティングを取得
  $routing = [];
  require_once('./routes/routing.php');

  // ルーティングからコントローラー名とアクションを取得
  $controller = '';
  $action = '';
  foreach($routing as $route) {
    if($route['path'] === $path && $route['method'] === $method) {
      $controller = $route['controller'];
      $action = $route['action'];
    }
  }

  if(empty($controller) || empty($action)) {
    throw new Exception();
  } else {
    $controller = new $controller;

    // 実行
    $controller->executeAction($action, $request);
  }
} catch(Exception $e) {
  header("HTTP/1.0 404 Not Found");
  echo(file_get_contents("./view/404.html"));
  exit;
}
