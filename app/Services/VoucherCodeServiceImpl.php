<?php
namespace App\Services;

use App\Services\VoucherCodeService;
  
class VoucherCodeServiceImpl implements VoucherCodeService
{
    /**
     * Pattern RegEx.
     *
     * @format XXXX-XXXX
     *
     * @var string
     */
    const REGEX = "/^[\w\d]{4}\-[\w\d]{4}$/";

    /**
     * Generate some semi random data.
     *
     * @return string
     */
    public function part() :string
    {
        return bin2hex(openssl_random_pseudo_bytes(2));
    }

    /**
     * Return a voucher code.
     *
     * @return string
     */
    public function generate() :string
    {
        return strtoupper(sprintf('%s-%s', $this->part(), $this->part()));
    }

    /**
     * Validate a voucher code.
     *
     * @param string $code
     *
     * @return bool
     */
    public function validate(string $code) :bool
    {
        return (bool) preg_match(self::REGEX, $code);
    }
}
