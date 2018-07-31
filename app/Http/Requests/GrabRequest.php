<?php

namespace App\Http\Requests;

use App\Services\VoucherCodeService;

class GrabRequest extends FormRequest
{
    public $code;

    public function __construct(VoucherCodeService $voucherCodeService)
    {
        $this->code = $voucherCodeService->generate();
    }

    public function rules()
    {
        return [
            'email' => 'required|email',
            'voucher_id' => 'required|exists:vouchers,id'
        ];
    }

    public function messages()
    {
        return [];
    }
}
