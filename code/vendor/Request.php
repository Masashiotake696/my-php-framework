<?php

class Request {
  private $query;

  /**
   * queryプロパティにパラメータの連想配列を格納する
   *
   * @param hash_array $query パラメータの連想配列
   */
  public function __construct($query) {
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
   * @params hash_array $params バリデーションを行う項目とその判定方法を格納した連想配列
   */
  public function validate($params) {
    $errors = [];
    foreach($params as $key => $array) {
      foreach($array as $method) {
        if(strpos($method, ':') !== FALSE) { // ':'で条件を指定しているか判定
          if($this->$method($key) !== TRUE) { // バリデーション
            $errors[$key][] = $this->messages($key, $method); // エラーがあったらメッセージを格納
          }
        } else {
          $array = explode(':', $method); // ':'で区切る
          $method = $array[0];
          $condition = $array[1];
          if($this->$method($key, $condition) !== TRUE) { // バリデーション
            $errors[$key][] = $this->messages($key, $method, $condition); // エラーがあったらメッセージを格納
          }
        }
      }
    }
  }

  /**
   * 値が入力されているか判定
   *
   * @param string $key 判定項目名
   * @return bool
   */
  private function required($key) {
    return count($this->getOneQuery($key)) > 0;
  }

  /**
   * 入力された値が文字列か判定
   *
   * @param string $key 判定項目名
   * @return bool
   */
  private function string($key) {
    return is_string($this->getOneQuery($key));
  }

  /**
   * 入力された値が数値か判定
   *
   * @param string $key 判定項目名
   * @return bool
   */
  private function numeric($key) {
    return is_int($this->getOneQuery($key));
  }

  /**
   * 入力された値が最小値で設定された条件を満たすか判定(文字列の場合は長さ、数値の場合は大きさ)
   *
   * @param string $key 判定項目名
   * @param string $condition 最小値条件
   * @return bool
   */
  private function min($key, $condition) {
    // 最小値条件を文字列から数値に変換する
    $condition = (int)$condition;

    // キーに該当する値を取得
    $query = $this->getOneQuery($key);

    // 値が空の場合はfalseを返す
    if(count($query) === 0) {
      return false;
    }

    // 入力された項目が数字の場合は値の大小を比較
    if(ctype_digit($query)) {
      $query = (int)$query; // 文字列から数値に変換
      return $query > $condition;
    }

    // 文字列の場合は値の長さを比較
    return count($query) > $condition;
  }

  /**
   * 入力された値が最大値で設定された条件を満たすか判定(文字列の場合は長さ、数値の場合は大きさ)
   *
   * @param string $key 判定項目名
   * @param string $condition 最大値条件
   * @return bool
   */
  private function max($key, $condition) {
    // 最大値条件を文字列から数値に変換する
    $condition = (int)$condition;

    // キーに該当する値を取得
    $query = $this->getOneQuery($key);

    // 値が空の場合はfalseを返す
    if(count($query) === 0) {
      return false;
    }

    // 入力された項目が数字の場合は値の大小を比較
    if(ctype_digit($query)) {
      $query = (int)$query; // 文字列から数値に変換
      return $query < $condition;
    }

    // 文字列の場合は値の長さを比較
    return count($query) < $condition;
  }

  /**
   * エラーメッセージを返す
   *
   * @param string $key エラーがあった項目名
   * @param string $method エラーがあったバリデーション条件
   * @param string $condition バリデーション時の条件
   * @return string エラーメッセージ
   */
  protected function messages($key, $method, $condition = null) {
    // 
  }
}
