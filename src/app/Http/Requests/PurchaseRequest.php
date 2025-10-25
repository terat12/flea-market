<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PurchaseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'product_id' => ['required', 'exists:products,id'],
            'payment'    => ['required', 'in:convenience,card'], // 支払い方法=選択必須
        ];
    }

    public function attributes(): array
    {
        return [
            'product_id' => '商品',
            'payment'    => '支払い方法',
        ];
    }

    public function messages(): array
    {
        return [
            'product_id.required' => '商品が選択されていません',
            'product_id.exists'   => '選択した商品が無効です',
            'payment.required'    => '支払い方法を選択してください',
            'payment.in'          => '支払い方法の値が不正です',
        ];
    }

    // 配送先が未設定ならバリデーションエラーを追加（「配送先=選択必須」を満たす）
    public function withValidator($validator): void
    {
        $validator->after(function ($v) {
            $u = $this->user();
            if (!$u || empty($u->zipcode) || empty($u->address)) {
                $v->errors()->add('shipping', '配送先が未設定です。プロフィールから設定してください。');
            }
        });
    }
}
