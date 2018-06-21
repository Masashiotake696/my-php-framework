<?php
class Validation {
    private $columns_and_methods = []; // 項目名とバリデーション項目
    private $error_column_and_methods = []; // エラー

    // 項目名とバリデーション項目を格納
    public function __construct($columns_and_methods) {
        $this->columns_and_methods = $columns_and_methods;
    }

    // 項目名とバリデーション項目を取得
    function getColumnsAndMethods() {
        return $this->columns_and_methods;
    }

    /**
     * エラーをプロパティにセット
     *
     * @param string $query クエリー(キーバリュー)
     * @return void
     */
    public function setErrors($query) {
        foreach($this->columns_and_methods as $column => $methods_list) {
            foreach($methods_list as $method) {
                $condition = null; // バリデーション条件を格納する変数s
                $type = null; // データ型

                // バリデーション条件を指定しているか判定
                if($this->hasCondition($method)) {
                  $method_and_condition = $this->getMethodAndCondition($method);
                  $method = $method_and_condition[0];
                  $condition = $method_and_condition[1];

                  // データ型を格納
                  if($this->checkColumnHasMethod($column, 'string')) {
                      $type = 'string';
                  } else if($this->checkColumnHasMethod($column, 'number')) {
                      $type = 'number';
                  }
                }

                // バリデーションを行い、エラーがあった場合にはerror_column_and_methodsプロパティにエラーがあったバリデーション項目を格納
                if($this->$method($query[$column], $condition, $type) !== TRUE) {
                    $this->error_column_and_methods[$column][] = $method;
                }
              }
        }
    }

    // エラーがあった項目名とそのバリデーション項目を取得
    public function getErrors() {
        return $this->error_column_and_methods;
    }

    // バリデーション条件を指定しているかを判定
    public function hasCondition($method) {
        return strpos($method, ':') !== FALSE;
    }

    /**
     * バリデーション項目とバリデーション条件を取得
     *
     * @param string $method バリデーション項目
     * @return array バリデーション項目とバリデーション条件を格納した配列
     */
    public function getMethodAndCondition($method) {
        // バリデーション条件を指定しているか判定
        if($this->hasCondition($method)) {
            $method_and_condition = explode(':', $method); // ':'で区切る
            return $method_and_condition;
        }
        return [$method, ''];
    }

    // エラーの有無を返す
    public function hasError() {
        return count($this->error_column_and_methods) > 0;
    }

    /**
     * ある項目名が特定のバリデーション項目を持つかを判定
     *
     * @param string $column 項目名
     * @param string $method バリデーション項目
     * @return bool
     */
    private function checkColumnHasMethod($column, $method) {
        // 特定の項目名がバリデーション項目を持つか判定
        if(array_key_exists($column, $this->columns_and_methods)) {
            // 特定のバリデーション項目を持つかを判定
            return in_array($method, (array)$this->columns_and_methods[$column]);
        }
    }

    /***** バリデーション用メソッド *****/

    /**
     * 値が入力されているか判定
     *
     * @param string $key 判定項目名
     * @return bool
     */
    private function require($key) {
        return mb_strlen($key) > 0;
    }

    /**
     * 入力された値が文字列か判定
     *
     * @param string $key 判定項目名
     * @return bool
     */
    private function string($key) {
        return is_string($key);
    }

    /**
     * 入力された値が数値か判定
     *
     * @param string $key 判定項目名
     * @return bool
     */
    private function number($key) {
        return is_numeric($key);
    }

    /**
     * 入力された値が最小値で設定された値以上かを判定
     *
     * @param string $key 判定項目名
     * @param string $condition 最小値条件
     * @param string $type データ型
     * @return bool
     */
    private function min($key, $condition, $type) {
        // 最小値条件を文字列から数値に変換する
        $condition = (int)$condition;

        // データ型によって場合分け
        switch($type) {
            case 'string':
                return mb_strlen($key) >= $condition;
            case 'number':
                return (int)$key >= $condition;
            default:
                // 入力された値の形式によって場合分け
                if(ctype_digit($key)) { // 数字の場合
                    return (int)$key >= $condition;
                } else { // 文字列の場合
                    return mb_strlen($key) >= $condition;
                }
        }
    }

    /**
     * 入力された値が最大値で設定された値以下かを判定
     *
     * @param string $key 判定項目名
     * @param string $condition 最大値条件
     * @return bool
     */
    private function max($key, $condition, $type) {
        // 最大値条件を文字列から数値に変換する
        $condition = intval($condition);

        // データ型によって場合分け
        switch($type) {
            case 'string':
                return mb_strlen($key) <= $condition;
            case 'number':
                return intval($key) <= $condition;
            default:
                // 入力された値の形式によって場合分け
                if(ctype_digit($key)) { // 数字の場合
                    return intval($key) <= $condition;
                } else { // 文字列の場合
                    return mb_strlen($key) <= $condition;
                }
        }
    }
}
