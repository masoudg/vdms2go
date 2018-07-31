<?php

namespace App\Http\Requests;

class RedeemRequest extends FormRequest
{
    public function rules()
    {
        return [
            'email' => 'required|email|exists:voucher_codes',
            'code' => 'required|exists:voucher_codes'
        ];
    }

    public function messages()
    {
        return [
            "email.exists" => "You are not eligible to redeem cause you don't have any voucher.",
            "code.exists" => "The code you entered is invalid."
        ];
    }
}
