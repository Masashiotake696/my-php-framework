<?php
class Template {
  private $template;

  /**
   * テンプレート(htmlファイル)をセットする
   *
   * @param string $file_name ファイル名(拡張子なし)
   * @param hash_array
   * @return void
   */
  public function __construct($fileName) {
    $this->template = file_get_contents('./view/' . $fileName . '.html');
  }

  /**
   * テンプレートを出力する
   *
   * @return string テンプレート内容
   */
  public function outputTemplate() {
    // テンプレート部分を空文字に変換
    $this->template = preg_replace('/(\{\{.+\}\})/', '', $this->template);
    echo $this->template;
  }

  /**
   * テンプレートにキーバリューの形で埋め込む値をセットする
   *
   * @param string $key キー
   * @param string $value バリュー
   * @return void
   */
  public function setKeyValue($key, $value) {
    $this->template = preg_replace("/(\{\{\s$key\s\}\})/", $value, $this->template);
  }

  /**
   * エラーを埋め込む
   *
   * @param hash_array $errors エラーの連想配列
   * @return void
   */
  public function setErrors($errors) {
    // エラーを出力するテンプレート部分を全て取得
    preg_match_all('/\{\{\s\$errors->(.+)\(\'(.+)\'\)\s\}\}/', $this->template, $matches);

    // 一つずつテンプレートにエラーメッセージを埋め込み
    for($i = 0; $i < count($matches); $i++) {
      $this->{$matches[1][$i]}($errors, $matches[2][$i]);
    }
  }

  /**
   * 入力値を埋め込む
   *
   * @param hash_array $query 入力値の連想配列
   */
  public function setOld($query) {
    // 入力値を出力するテンプレート部分を全て取得
    preg_match_all('/\{\{\s\$old\(\'(.+)\'\)\s\}\}/', $this->template, $matches);

    // 一つずつテンプレートにエラーメッセージを埋め込み
    for($i = 0; $i < count($query); $i++) {
      // パターンを定義
      $pattern = '/\{\{\s\$old\(\'' . $matches[1][$i] . '\'\)\s\}\}/';
      $this->template = preg_replace($pattern, $query[$matches[1][$i]], $this->template);
    }
  }

  /**
   * エラーの連想配列の一番目をエラーとして埋め込む
   *
   * @param hash_array $errors エラーの連想配列
   * @param string $key エラー項目名
   * @return void
   */
  private function first($errors, $key) {
    // パターンを定義
    $pattern = '/\{\{\s\$errors\->first\(\'' . $key . '\'\)\s\}\}/';
    // 文字列置換
    if(mb_strlen($errors[$key][0]) > 0) {
      $replacement = $errors[$key][0];
    } else {
      $replacement = '';
    }
    $this->template = preg_replace($pattern, $replacement, $this->template);
  }

  /**
   * エラーの連想配列の全てをエラーとして埋め込む
   *
   * @param hash_array $errors エラーの連想配列
   * @param string $key エラー項目名
   * @return void
   */
  private function all($errors, $key) {
    $pattern = '/\{\{\s\$errors\->all\(\'' . $key . '\'\)\s\}\}/';

    // 連結したエラーメッセージを格納する変数
    $error_messages = '';
    if(!is_null($errors[$key])) {
      // エラーメッセージを連結
      foreach($errors[$key] as $index => $error_message) {
        $error_messages .= $error_message;
        if((count($errors[$key]) - 1) !== $index) {
          $error_messages .= "\n";
        }
      }
    }
    // 文字列置換
    $this->template = preg_replace($pattern, $error_messages, $this->template);
  }

  /**
   * テンプレートに値を埋め込む
   *
   * @param string $fileContent テンプレート
   * @param array キーバリュー配列
   * @return void
   */
  public function replaceTemplate($params) {
    // {{}}の部分を取得
    preg_match_all('/\{\{(.*)\}\}/', $fileContent, $matches);
    foreach($matches[1] as $index => $value) {
      if(array_key_exists($value, $params)) {
        preg_replace("/\{\{\s$value\s\}\}/", $fileContent, $params[$value]);
      } else {
        preg_replace("/\{\{\s$value\s\}\}/", $fileContent, '');
      }
    }
  }
}
