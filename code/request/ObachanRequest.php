<?php
require_once('./vendor/Request.php');

class ObachanRequest extends Request {
    public function rules() {
        return [
            [
            'name' => ['string', 'min:1', 'max:20'],
            'age' => ['number', 'require', 'min:0', 'max:130'],
            'address' => ['string', 'require'],
            ],
            'obachan'
        ];
    }

    public function messages() {
        return [
            'name.string' => '名前は文字列で入力してください',
            'name.min' => '名前は1文字以上にしてください',
            'name.max' => '名前は20文字以下にしてください',
            'age.number' => '年齢は数値で入力してください',
            'age.require' => '年齢を入力してください',
            'age.min' => '年齢は0以上にしてください',
            'age.max' => '年齢は130以下にしてください',
            'address.string' => '住所は文字列にしてください',
            'address.require' => '住所を入力してください',
        ];
    }
}
