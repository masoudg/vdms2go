<?php

namespace App\Http\Requests;

class UserVouchersListRequest extends FormRequest
{
    public function rules()
    {
        return [
            'email' => 'required|email|exists:voucher_codes'
        ];
    }

    public function messages()
    {
        return [
            "email.exists" => "You don't have any voucher."
        ];
    }
}
