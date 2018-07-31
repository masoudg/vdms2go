<?php

namespace Tests\Feature;

use Tests\TestCase;

class VoucherTest extends TestCase
{
    public function testDescription()
    {
        $this->json("get", "/")
             ->seeJson([
                "service_name" => "VDMS"
             ]);
    }

    public function testAllVouchersList()
    {
        $response = $this->get("/voucher/index");
        $response->assertResponseStatus(200);
    }
}
