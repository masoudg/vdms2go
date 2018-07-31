<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Voucher;
use App\Models\VoucherCode;

class VoucherCodeTest extends TestCase
{
    /**
     * Create a voucher
     */
    public function testCreateVoucher()
    {
        $voucher = factory("App\Models\Voucher")->create();
        $this->assertInstanceOf("App\Models\Voucher", $voucher);

        $storedVoucher = Voucher::find($voucher->id);
        $this->assertEquals($voucher->name, $storedVoucher->name);
        $this->assertEquals($voucher->percentage_discount, $storedVoucher->percentage_discount);
        $this->assertEquals($voucher->validity_period, $storedVoucher->validity_period);
        return $voucher;
    }

    public function testAllCodesList()
    {
        // test status code of the api response 
        $response = $this->get("/voucher/code/index");
        $response->assertResponseStatus(200);
    }

    public function testUserVoucherCodesList()
    {
        // test email required validation
        $response1 = $this->post("/voucher/user/index")
                        ->seeJson([ "message" => "The email field is required." ]);
        $response1->assertResponseStatus(422);

        // test email format validation
        $response2 = $this->post("/voucher/user/index", ["email" => "masoud@"])
                        ->seeJson([ "message" => "The email must be a valid email address." ]);
        $response2->assertResponseStatus(422);

        // test invalid email (owner)
        $response3 = $this->post("/voucher/user/index", ["email" => "masoud@gh.com"])
                        ->seeJson([ "message" => "You don't have any voucher." ]);
        $response3->assertResponseStatus(422);
    }

    /**
     * @depends testCreateVoucher
     */
    public function testGrab(Voucher $voucher)
    {
        // test email required validation
        $response1 = $this->post("/voucher/grab")
                        ->seeJson([ "message" => "The email field is required." ]);
        $response1->assertResponseStatus(422);

        // test email format validation
        $response2 = $this->post("/voucher/grab", ["email" => "masoud@"])
                        ->seeJson([ "message" => "The email must be a valid email address." ]);
        $response2->assertResponseStatus(422);

        // test voucher_id required validation
        $response3 = $this->post("/voucher/grab", ["email" => "masoud@gh.com"])
                        ->seeJson([ "message" => "The voucher id field is required." ]);
        $response3->assertResponseStatus(422);

        // test invalid voucher_id
        $response4 = $this->post("/voucher/grab", ["email" => "masoud@gmail.com", "voucher_id"=>999999])
                        ->seeJson([ "message" => "The selected voucher id is invalid." ]);
        $response4->assertResponseStatus(422);

        // test successfull grab request
        $response4 = $this->post("/voucher/grab", ["email" => "masoud@gmail.com", "voucher_id"=>$voucher->id])
                        ->seeJson([ "message" => "Congratulations! You just unlocked a voucher code." ]);
        $response4->assertResponseStatus(200);
        $response4 = json_decode($response4->response->getContent());
        return $response4->data->code;
    }

    /**
     * Remove the test data
     * @depends testGrab
     */
    public function testRedeem(String $code)
    {
        // test email required validation
        $response1 = $this->post("/voucher/redeem")
                        ->seeJson([ "message" => "The email field is required." ]);
        $response1->assertResponseStatus(422);

        // test email format validation
        $response2 = $this->post("/voucher/redeem", ["email" => "masoud@"])
                        ->seeJson([ "message" => "The email must be a valid email address." ]);
        $response2->assertResponseStatus(422);

        // test invalid email (owner)
        $response3 = $this->post("/voucher/redeem", ["email" => "masoud@gh.com"])
                        ->seeJson([ "message" => "You are not eligible to redeem cause you don't have any voucher." ]);
        $response3->assertResponseStatus(422);

        // test code required validation
        $response4 = $this->post("/voucher/redeem", ["email" => "masoud@gmail.com"])
                        ->seeJson([ "message" => "The code field is required." ]);
        $response4->assertResponseStatus(422);

        // test invalid code
        $response5 = $this->post("/voucher/redeem", ["email" => "masoud@gmail.com", "code"=>"9990-99"])
                        ->seeJson([ "message" => "The code you entered is invalid." ]);
        $response5->assertResponseStatus(422);

        // test successfull redeem request
        $response5 = $this->post("/voucher/redeem", ["email" => "masoud@gmail.com", "code"=>$code])
                        ->seeJson([ "message" => "Congratulations! You just redeemed your voucher." ]);
        $response5->assertResponseStatus(200);
        return $code;
    }

    /**
     * Remove the test data
     * @depends testCreateVoucher
     * @depends testRedeem
     */
    public function testTearDownData(Voucher $voucher, String $code)
    {
        VoucherCode::where("code", $code)->delete();
        $isVoucherDestroyed = Voucher::destroy($voucher->id);
        $this->assertEquals(1, $isVoucherDestroyed);
    }
}
