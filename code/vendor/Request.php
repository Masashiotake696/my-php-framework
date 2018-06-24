<?php

require_once('./vendor/Validation.php');

class Request {
  private $path; // リクエストパス
  private $query; // クエリパラメータ
  private $validation; // バリデーションインスタンス
  private $errors = []; // viewに渡すエラー

  /**
   * pathプロパティにリクエストのパスを、queryプロパティにパラメータの連想配列を格納する
   *
   * @param string $path パス
   * @param hash_array $query パラメータの連想配列
   */
  public function __construct($path, $query = []) {
    $this->path = $path;
    $this->query = $query;
  }

  /**
   * クエリのゲッター(全て)
   *
   * @return array $this->query 全てのクエリを格納した連想配列
   */
  public function getAllQuery() {
    return $this->query;
  }

  /**
   * クエリのゲッター(特定)
   *
   * @param string $queryKey 取得したいクエリのキー
   * @return array $this->query 全てのクエリを格納した連想配列
   */
  public function getOneQuery($queryKey) {
    return $this->query[$queryKey];
  }

  /**
   * バリデーションを行い、trueの場合は何もせず、falseの場合はエラーメッセージと共にリダイレクト
   *
   * @param hash_array $params バリデーションを行う項目とその判定方法を格納した連想配列
   * @param string $view バリデーションエラーがあった場合に表示するビュー
   * @return bool
   */
  public function validate($params, $view = null) {
    // バリデーションインスタンスをプロパティに格納
    $this->validation = new Validation($params);

    // クエリを元にバリデーションを行い、エラーがあった場合に項目名とそのバリデーション項目をセット
    $this->validation->setErrors($this->query);

    // エラーが存在していたらエラープロパティにその値をセット
    if($this->validation->hasError()) {
      $this->setErrorMessages();
      // viewがセットされている場合はviewを表示
      if(!is_null($view)) {
        $this->outputView($view);
      }
      return false;
    }

    return true;
  }

  /**
   * エラーメッセージセッター
   *
   * @return void
   */
  private function setErrorMessages() {

    // エラーメッセージを取得
    $messages = $this->messages();

    // エラーがあった項目名とそのバリデーション項目を取得
    $error_array = $this->validation->getErrors();

    // エラーがあった項目に対してメッセージをセットする
    foreach($error_array as $key => $methods) {
      foreach($methods as $method) {
        if(array_key_exists("{$key}.{$method}", $messages)) {
          // プロパティにキーバリューの形で格納
          $this->errors[$key][] = $messages["{$key}.{$method}"];
        }
      }
    }
  }

  /**
   * エラーメッセージ取得用ゲッターメソッド
   *
   * @return array $errors エラーメッセージプロパティ
   */
  public function getErrorMessages() {
    return $this->errors;
  }

  /**
   * エラーメッセージを返す
   *
   * @return array $error_messages 「key.method => エラーメッセージ」の形で格納した配列
   */
  protected function messages() {
    // エラーメッセージ格納用配列
    $error_messages = [];

    // 項目名、バリデーション項目、バリデーション条件を元にエラーメッセージを作成する
    foreach($this->validation->getColumnsAndMethods() as $column => $methods) {
      foreach($methods as $method) {
        $condition = '';
        // バリデーション項目にバリデーション条件が指定されているか判定
        if($this->validation->hasCondition($method)) {
          $method_and_condition = $this->validation->getMethodAndCondition($method);
          $method = $method_and_condition[0];
          $condition = $method_and_condition[1];
        }
        // 各バリデーション項目に応じてエラーメッセージを作成する
        switch($method) {
          case 'require':
            $error_messages["{$column}.{$method}"] = "{$column} is required";
            break;
          case 'number':
            $error_messages["{$column}.{$method}"] = "{$column} must be number";
            break;
          case 'string':
            $error_messages["{$column}.{$method}"] = "{$column} must be string";
            break;
          case 'min':
            $error_messages["{$column}.{$method}"] = "{$column} must be {$condition} or more";
            break;
          case 'max':
            $error_messages["{$column}.{$method}"] = "{$column} must be {$condition} or less";
            break;
        }
      }
    }

    return $error_messages;
  }

  /**
   * Viewを表示
   *
   * @param string $view 表示するビュー
   * @return void
   */
  private function outputView($view) {
    // テンプレートインスタンスの生成
    $template = new Template($view);

    // エラーをセット
    $template->setErrors($this->errors);

    // 入力値をセット
    $template->setOld($this->query);

    // ビューの表示
    $template->outputTemplate();
    exit;
  }
}
