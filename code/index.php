<?php
// vendorファイルの読み込み
require_once('./vendor/routes.php');
require_once('./vendor/template.php');
require_once('./vendor/controller/BaseController.php');

// コントローラーのファイルを読み込み
spl_autoload_register(function($class_name) {
  require_once('controller/' . $class_name . '.php');
});

// 処理を行うコントローラーを生成
$route = new Route();
$controllerName = $route->getControllerName();
$controller = new $controllerName;
// 実行
$controller->run();

// クエリを取得
$param = $route->getQuery();

// echo display('index.html', $param);
?>
