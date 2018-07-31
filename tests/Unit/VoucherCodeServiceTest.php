<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\VoucherCodeServiceImpl;

class VoucherCodeServiceTest extends TestCase
{
    public function testGenerateCode()
    {
        // test code length and validation
        $voucherCodeService = new VoucherCodeServiceImpl();
        $code = $voucherCodeService->generate();
        $this->assertEquals(9, strlen($code));
        $this->assertTrue($voucherCodeService->validate($code));
    }
}
