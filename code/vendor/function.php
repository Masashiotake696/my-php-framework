<?php
// リクエストメソッドがGETか判定
function isRequestGet() {
  return $_SERVER['REQUEST_METHOD'] === 'GET';
}

// リクエストメソッドがPOSTか判定
function isRequestPost() {
  return $_SERVER['REQUEST_METHOD'] === 'POST';
}

// リクエストURLを返す
function getRequestUrl() {
  return $_SERVER['REQUEST_URI'];
}

// 特殊文字をHTMLエンティティに変換
function h($s) {
  return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}

// URLに応じて表示するメッセージを分ける
function allocateMessageByURL() {
  $url = getRequestUrl();
  switch(true) {
    case $url === '/obachan':
      return '元気です';
    case $url === '/hasumin':
      return 'Androidラブ';
    case preg_match('/^\/oba\?obavar=.+$/', $url) === 1:
      return ltrim(strstr($_SERVER['QUERY_STRING'], '='), '=');
    default:
      return '誰?';
  }
}
