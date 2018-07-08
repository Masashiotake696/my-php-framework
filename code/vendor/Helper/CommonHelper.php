<?php

class CommonHelper {
    /**
     * 引数で渡された値の型が文字列型か整数型であるかを判定
     *
     * @param something $value 型判定する値
     * @return boolean
     */
    public static function typeIsStringOrInteger($value) {
        // 引数で渡された値の型を取得
        $value_type = gettype($value);

        // 判定
        if($value_type !== 'integer' && $value_type !== 'string') {
            return false;
        } else {
            return true;
        }
    }
}
