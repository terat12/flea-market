<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // 認証ミドルウェアで守られてる？？
    }

    public function rules(): array
    {
        return [
            'title'       => ['required', 'string', 'max:100'],
            'brand'       => ['nullable', 'string', 'max:50'],
            'description' => ['required', 'string', 'max:255'],
            'price'       => ['required', 'integer', 'min:0'],
            'condition'   => ['required', 'integer', 'between:1,6'],
            'categories'    => ['required', 'array', 'min:1'], 
            'categories.*'  => ['string', 'max:50'],
            'image'         => ['required', 'mimes:jpeg,png'],
        ];
    }
    public function attributes(): array
    {
        return [
            'title'       => '商品名',
            'description' => '商品説明',
            'price'       => '販売価格',
            'condition'   => '商品の状態',
            'categories'  => '商品のカテゴリー',
            'image'       => '商品画像',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required'       => '商品名を入力してください',
            'description.required' => '商品説明を入力してください',
            'description.max'      => '商品説明は255文字以内で入力してください',
            'price.required'       => '販売価格を入力してください',
            'price.integer'        => '販売価格は数値で入力してください',
            'price.min'            => '販売価格は0円以上で入力してください',
            'condition.required'   => '商品の状態を選択してください',
            'categories.required'  => '商品のカテゴリーを選択してください',
            'categories.array'     => '商品のカテゴリーの形式が不正です',
            'categories.min'       => '商品のカテゴリーは少なくとも1つ選択してください',
            'image.required'       => '商品画像をアップロードしてください',
            'image.mimes'          => '商品画像は.jpeg もしくは .png を指定してください',
        ];
    }
}
