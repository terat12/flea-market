<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CommentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'body' => ['required', 'string', 'max:255'],
        ];
    }

    public function attributes(): array
    {
        return ['body' => 'コメント'];
    }

    public function messages(): array
    {
        return [
            'body.required' => 'コメントを入力してください',
            'body.max'      => 'コメントは最大255文字で入力してください',
        ];
    }
}
