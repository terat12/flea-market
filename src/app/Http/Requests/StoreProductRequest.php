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
            'description' => ['nullable', 'string', 'max:1000'],
            'price'       => ['required', 'integer', 'min:1', 'max:9999999'],
            'condition'   => ['required', 'integer', 'between:1,6'],
            'category'    => ['nullable', 'string', 'max:50'],
            'image'       => ['nullable', 'image', 'max:2048'], // 2MB
        ];
    }

    public function attributes(): array
    {
        return [
            'title' => '商品名',
            'price' => '販売価格',
            'condition' => '商品の状態',
            'image' => '商品画像',
        ];
    }
}
