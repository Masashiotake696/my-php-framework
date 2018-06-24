<?php
// vendorファイルの読み込み
require_once('./vendor/Request.php');
require_once('./vendor/Template.php');
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

  // クエリを取得(HTTPメソッドがGET以外の場合はHTTPリクエストのボディからクエリを取得)
  if($method !== 'GET') {
    $query = file_get_contents('php://input');
  } else {
    $query = $_SERVER['QUERY_STRING'];
  }

  // クエリを格納する連想配列
  $query_hash = [];

  // 連想配列の形でクエリを格納
  if(!empty($query)) {
    // &で区切る
    $query_array = explode('&', $query);

    foreach($query_array as $query) {
      // &で区切ったクエリが'='を含むか判定
      if(strpos($query, '=') !== FALSE) {
        $query = explode('=', $query);
        // =で分割したクエリがX=Yの形になっているか判定
        if(count($query) === 2) {
          $query_hash[$query[0]] = $query[1];
        }
      }
    }
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
    // コントローラー名を保持
    $controller_name = $controller;

    // コントローラーインスタンスを生成
    $controller = new $controller;

    // フォームリクエストの対応表を取得
    require_once('./request/form_request.php');

    // 該当するフォームリクエストを取得
    $form_request_name = '';
    foreach($form_requests as $form_request) {
      if(
        $form_request['controller'] === $controller_name &&
        $form_request['action'] === $action
      ) {
        $form_request_name = $form_request['request'];
      }
    }

    if(!empty($form_request_name)) {
      require_once("./request/{$form_request_name}.php");

      // フォームリクエストをインスタンス化
      $request = new $form_request_name($path, $query_hash);
      // ルールを実行する
      $rules = $request->rules();

      // バリデーションを実行してエラーがあった場合は指定されたビューを表示
      if($request->validate($rules[0]) === FALSE) {
        // テンプレートインスタンスの生成
        $template = new Template($rules[1]);

        // エラーをセット
        $template->setErrors($request->getErrorMessages());

        // 入力値をセット
        $template->setOld($request->getAllQuery());

        // ビューの表示
        $template->outputTemplate();
        exit;
      }
    } else {
      // リクエストインスタンスを生成
      $request = new Request($path, $query_hash);
    }

    // アクションを実行
    $controller->executeAction($action, $request);
  }
} catch(Exception $e) {
  // 該当するページが見つからない場合は404ページを表示
  header("HTTP/1.0 404 Not Found");
  echo(file_get_contents("./view/404.html"));
  exit;
}
