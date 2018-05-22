<?php
// vendorファイルの読み込み
require_once('./vendor/routes.php');
require_once('./vendor/controller/BaseController.php');

// コントローラーのファイルを読み込み
spl_autoload_register(function($className) {
  if(strpos($className, 'Controller')) {
    require_once('controller/' . $className . '.php');
  }
});

// 処理を行うコントローラーを生成
$controllerName = Routes::getControllerName();
$controller = new $controllerName;

// 実行
$controller->action();
?>
