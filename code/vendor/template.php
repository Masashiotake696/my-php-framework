<?php
class Template {
  private $template;

  /*
  * テンプレート(htmlファイル)をセットする
  * @param string $file_name ファイル名(拡張子なし)
  */
  public function __construct($fileName) {
    $this->template = file_get_contents('./view/' . $fileName . '.html');
  }

  /*
  * テンプレートを出力する
  * @return string テンプレート内容
  */
  public function outputTemplate() {
    echo $this->template;
  }

  /*
  * テンプレートに埋め込む値をセットする
  * @param1 string $key キー
  * @param2 string $value バリュー
  */
  public function setTemplateKeyValue($key, $value) {
    $this->template = preg_replace("/(\{\{\s$key\s\}\})/", $value, $this->template);
  }

  /*
  * テンプレートに値を埋め込む
  * @param1 string $fileContent テンプレート
  * @param2 array キーバリュー配列
  * @return string 値を埋め込んだテンプレート
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
