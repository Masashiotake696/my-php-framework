<?php
require_once('./vendor/controller/DatabaseController.php');
class Model {
    protected $primaryKey = 'id';

    /**
     * 
     */
    public function __construct() {
    }

    /**
     * primary_keyで指定されたカラムを元に該当するレコードを取得する
     *
     * @param string|integer| primary_keyの値
     * @return object 指定された値に該当するレコードのインスタンス
     */
    public static function find($value) {
        // pdo,モデル名,テーブル名を格納した配列を取得
        $preprocessing_array = self::preprocessing();

        // findメソッドで指定されたクラスのインスタンスを生成
        $instance = new $preprocessing_array['modelName'];

        // SQLを生成
        $stmt = $preprocessing_array['pdo']
            ->prepare('SELECT * FROM ' . $preprocessing_array['tableName'] . ' WHERE ' . $instance->primaryKey . ' = ?');
        // パラメータの型からPDOで指定する型を取得
        switch(gettype($value)) {
            case 'integer':
                $value = (int)$value;
                $pdo_param = PDO::PARAM_INT;
                break;
            case 'string':
                $pdo_param = PDO::PARAM_STR;
                break;
            default:
                $value = (int)$value;
                $pdo_param = PDO::PARAM_INT;
                break;
        }
        // ?に値をバインド
        $stmt->bindValue(1, $value, $pdo_param);
        // SQLを実行
        $stmt->execute();
        // レコードを取得
        $row = $stmt->fetch();

        // インスタンスにプロパティを動的に追加
        foreach($row as $key => $value) {
            $instance->{$key} = $value;
        }

        // インスタンスを返却
        return $instance;
    }

    /**
     * コール元クラスと同名のテーブルからレコードを全件取得してそれぞれをインスタンスとして格納した配列を返却
     *
     * @return array $return_array インスタンスを格納した配列
     */
    public static function all() {
        // pdo,モデル名,テーブル名を格納した配列を取得
        $preprocessing_array = self::preprocessing();

        // SQLを実行
        $stmt = $preprocessing_array['pdo']
            ->query('SELECT * FROM ' . $preprocessing_array['tableName']);

        // 返却用配列
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

    /**
     * 
     */
    public static function first() {

    }

    /**
     * 
     */
    public static function findBy() {

    }

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
}
