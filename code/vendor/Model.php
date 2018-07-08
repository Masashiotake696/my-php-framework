<?php
require_once('./vendor/controller/DatabaseController.php');
require_once('./vendor/OreOreException.php');
require_once('./vendor/Helper/CommonHelper.php');

class Model {
    protected $primaryKey = 'id';

    /*--------------- READ(取得) ---------------*/

    /**
     * primary_keyで指定されたカラムを元に該当するレコードを返却
     *
     * @param string|integer|array primary_keyの値
     * @return object|array 指定された値に該当するレコードのインスタンス(指定された値が複数あった場合はインスタンスを格納した配列)
     */
    public static function find($value) {
        // pdo,モデル名,テーブル名を格納した配列を取得
        $preprocessing_array = self::preprocessing();
        // findメソッドで指定されたクラスのインスタンスを生成
        $instance = new $preprocessing_array['modelName'];
        // 渡された値の型によって処理を切り分ける
        if(gettype($value) === 'array') {
            // 配列の中身の要素の型チェック
            array_map(function($e) {
                if(!CommonHelper::typeIsStringOrInteger($e)) {
                    throw new OreOreException('Erorr: The contents of the array of the argument must be string or integer');
                }
            }, $value);
            // 被演算子に配列の長さ分だけの?を格納
            $operand = '(' . substr(str_repeat(',?', count($value)), 1) . ')';
            // 演算子にINを格納
            $operator = ' IN ';
        } else if(CommonHelper::typeIsStringOrInteger($value)) {
            // 配列でない場合は配列に格納
            $value = [$value];
            // 被演算子に?を格納
            $operand = '?';
            // 演算子に=を格納
            $operator = ' = ';
        } else {
            // 数値でも文字列でもない場合は例外を投げる
            throw new OreOreException('Erorr: Arguments must be array or string or integer');
        }
        // SQLを生成
        $stmt = $preprocessing_array['pdo']
            ->prepare('SELECT * FROM ' . $preprocessing_array['tableName'] . ' WHERE ' . $instance->primaryKey . $operator . $operand);
        for($i = 0; $i < count($value); $i++) {
            // バインドする値の型に対応したPDO定義済み定数を取得
            $pdo_param_type = self::getPdoType($value[$i]);
            // ?に値をバインド
            $stmt->bindValue((int)$i + 1, $value[$i], $pdo_param_type);
        }
        // SQLを実行
        $stmt->execute();
        // 返却用配列を定義
        $return_array = [];
        // 値を繰り返し取得
        while($row = $stmt->fetch()) {
            // インスタンスを生成
            $instance = new $preprocessing_array['modelName'];
            // インスタンスにプロパティを動的に追加
            foreach($row as $key => $value) {
                $instance->{$key} = $value;
            }
            $return_array[] = $instance;
        }
        // 返却するインスタンスの数に応じて処理を切り分ける
        if(count($return_array) === 1) { // 返却するインスタンスが一つだった場合はそのインスタンスを返却
            return $return_array[0];
        } else if(count($return_array) === 0) { // 返却するインスタンスが存在しない場合は空文字を返却
            return '';
        } else { // 返却するインスタンスが一つ以上の場合はインスタンスを格納した配列を返却
            return $return_array;
        }
    }

    /**
     * レコードを全件取得してそれぞれをインスタンスとして格納した配列を返却
     *
     * @return array $return_array インスタンスを格納した配列
     */
    public static function all() {
        // pdo,モデル名,テーブル名を格納した配列を取得
        $preprocessing_array = self::preprocessing();
        // SQLを実行
        $stmt = $preprocessing_array['pdo']
            ->query('SELECT * FROM ' . $preprocessing_array['tableName']);
        // 返却用配列を定義
        $return_array = [];
        // Userクラスのインスタンスとして登録
        while($row = $stmt->fetch()) {
            // allメソッドで指定されたクラスのインスタンスを生成
            $instance = new $preprocessing_array['modelName'];
            // インスタンスにプロパティを動的に追加
            foreach($row as $key => $value) {
                $instance->{$key} = $value;
            }
            // 返却用配列にインスタンスを格納
            $return_array[] = $instance;
        }
        // インスタンスを格納した配列を返却
        return $return_array;
    }

    /*--------------- INSERT, UPDATE(更新) ---------------*/

    /**
     * インスタンスが持つプロパティの値を元にデータベースのレコードを更新または作成
     *
     * @return void
     */
    public function save() {
        // primaryKeyプロパティの値を取得
        $primaryKeyName = $this->primaryKey;
        // インスタンスのprimaryKeyプロパティの値を取得
        $primaryKeyValue = $this->$primaryKeyName;
        // インスタンスのprimaryKeyプロパティの値が空の場合はinsert処理を行う
        if(empty($primaryKeyValue)) {
            $this->insert();
            return;
        }
        // オブジェクトのクラス名を取得
        $className = get_class($this);
        // primaryKeyの値を元にfind
        $findValue = $className::find(((int)$primaryKeyValue));
        // findした値が空の場合はinsert、空じゃない場合はupdateを実行
        if(empty($findValue)) {
            $this->insert();
        } else {
            $this->update($findValue);
        }
        return;
    }

    /**
     * 引数で受け取ったインスタンスを元に差分があればレコードを更新
     *
     * @return void
     */
    private function update(Model $instance) {
        // 変更後インスタンスの値を配列に変換して、primaryKeyを除いた配列を取得
        $update_params_array = $this->getArrayOfInstanceExceptPrimaryKey($this);
        // KeyがprimaryKeyが指す値である要素を削除
        unset($update_params_array[$this->primaryKey]);
        // PDOで値を更新するために使用するカラムとその値を指定した文字列を作成
        $update_params_str = '';
        foreach($update_params_array as $key => $value) {
            // 変更前と値が同じ場合はその要素を削除して次のループへ
            if($value === $instance->$key) {
                unset($update_params_array[$key]);
                array_values($update_params_array);
                continue;
            }
            // 文字列が空じゃない場合はカンマを追加
            if(!empty($update_params_str)) {
                $update_params_str .= ', ';
            }
            // 更新するカラムとbind値を文字列連結
            $update_params_str .= $key . ' = :' . $key;
        }
        // PDOで値を更新するための文字列が空の場合は処理を終了
        if(mb_strlen($update_params_str) === 0) {
            return;
        }
        // テーブル名を取得
        $tableName = mb_strtolower(get_class($instance));
        // PDOを取得
        $pdo = DatabaseController::getPdo();
        // SQLを生成
        $stmt = $pdo->prepare('UPDATE ' . $tableName . ' SET ' . $update_params_str . ' WHERE ' . $this->primaryKey . ' = :primaryKey');
        // primaryKeyプロパティの値を取得
        $primaryKeyName = $this->primaryKey;
        // インスタンスのprimaryKeyプロパティの値を取得
        $primaryKeyValue = $this->$primaryKeyName;
        // インスタンスのprimaryKeyプロパティの型に対応するPDO定義済み定数を取得
        $primaryKeyType = self::getPdoType($primaryKeyValue);
        // primaryKeyの値をセット
        $stmt->bindValue(':primaryKey', $primaryKeyValue, $primaryKeyType);
        // 変更するカラムとprimaryKeyに値をセット
        foreach($update_params_array as $key => $value) {
            // バインドする値の型に対応したPDO定義済み定数を取得
            $pdo_param_type = self::getPdoType($value);
            // バインドする値にプロパティ名を指定してバインド
            $stmt->bindValue($key, $value, $pdo_param_type);
        }
        // 更新
        $stmt->execute();
        return;
    }

    /**
     * インスタンス情報をもとに新しいレコードを追加
     *
     * @return void
     */
    private function insert() {
        // 作成するインスタンスの値を配列に変換して、primaryKeyを除いた連想配列を取得
        $insert_params_array = $this->getArrayOfInstanceExceptPrimaryKey($this);
        // 連想配列のキーのみの配列を取得
        $insert_keys_array = array_keys($insert_params_array);
        // PDOで値を入れるカラムを指定した文字列を作成
        $insert_columns_str = implode(', ', $insert_keys_array);
        // PDOで入れる値を指定した文字列を作成
        $insert_values_str = implode(', ', array_map(function($e) {
            return ':' . $e;
        }, $insert_keys_array));
        // PDOで値を作成するための文字列が空の場合は処理を終了
        if(mb_strlen($insert_columns_str) === 0 || mb_strlen($insert_values_str) === 0) {
            return;
        }
        // テーブル名を取得
        $tableName = mb_strtolower(get_class($this));
        // PDOを取得
        $pdo = DatabaseController::getPdo();
        // SQLを生成
        $stmt = $pdo->prepare('INSERT INTO ' . $tableName . '(' . $insert_columns_str . ') VALUES (' . $insert_values_str . ')');
        // stmtに値をバインド
        foreach($insert_params_array as $key => $value) {
            // バインドする値の型に対応したPDO定義済み定数を取得
            $pdo_param_type = self::getPdoType($value);
            // バインドする値にプロパティ名を指定してバインド
            $stmt->bindValue($key, $value, $pdo_param_type);
        }
        // 作成
        $stmt->execute();
        return;
    }


    /*--------------- DELETE(削除) ---------------*/
    /**
     * 指定されたインスタンスに該当するレコードを削除
     *
     * @return void
     */
    public function delete() {
        // primaryKeyの値を取得
        $primaryKeyName = $this->primaryKey;
        // primaryKeyが指すプロパティの値を取得
        $primaryKeyValue = $this->$primaryKeyName;
        // primaryKeyが指すプロパティの値が空の場合は例外を投げる
        if(empty($primaryKeyValue)) {
            throw new OreOreException("Instance does not have value of {$primaryKeyName}");
        }
        // テーブル名を取得
        $tableName = mb_strtolower(get_class($this));
        // PDOを取得
        $pdo = DatabaseController::getPdo();
        // SQLを生成
        $stmt = $pdo->prepare('DELETE FROM ' . $tableName . ' WHERE ' . $this->primaryKey . ' = :primaryKey');
        // インスタンスのprimaryKeyプロパティの型に対応するPDO定義済み定数を取得
        $primaryKeyType = self::getPdoType($primaryKeyValue);
        // primaryKeyをバインド
        $stmt->bindValue(':primaryKey', $primaryKeyValue, $primaryKeyType);
        // 削除
        $stmt->execute();
        return;
    }


    /*--------------- その他privateメソッド ---------------*/

    /**
     * 前処理として、pdo,モデル名,テーブル名を格納した配列を返却
     *
     * @return object pdo,モデル名,テーブル名を格納した配列
     */
    private function preprocessing() {
        // 返却用配列
        $return_array = [];
        // PDOを取得
        $return_array['pdo'] = DatabaseController::getPdo();
        // 静的メソッドのコール元のクラス名を取得
        $return_array['modelName'] = get_called_class();
        // 先頭の文字を小文字に変換
        $return_array['tableName'] = mb_strtolower($return_array['modelName']);
        return $return_array;
    }

    /**
     * 引数で受け取った値の型に該当するPDO定義済み定数を返却
     *
     * @param something $value 判定したい値
     * @return constant POD定義済み定数
     */
    private function getPdoType($value) {
        $type = gettype($value);
        if($type === 'integer') {
            return PDO::PARAM_INT;
        } else if($type === 'string') {
            return PDO::PARAM_STR;
        } else {
            throw new OreOreException('Invalid Value in property');
        }
    }

    /**
     * インスタンスを配列に変換して、primaryKeyを除いた配列を返却
     *
     * @return array インスタンスからpirmaryKeyを除いた配列
     */
    private function getArrayOfInstanceExceptPrimaryKey(Model $instance) {
        // インスタンスの値を配列に変換
        $parmas_array = get_object_vars($instance);
        // KeyがprimaryKeyである要素を削除
        unset($parmas_array['primaryKey']);
        return $parmas_array;
    }
}
