<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Voucher;
use App\Models\VoucherCode;
use App\Services\VoucherCodeServiceImpl;
use App\Http\Requests\GrabRequest;
use App\Http\Requests\RedeemRequest;
use App\Http\Requests\UserVouchersListRequest;

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

    /**
     * Create a grab request
     * @depends testCreateVoucher
     */
    public function testGrabRequest(Voucher $voucher)
    {
    	$grabRequest = new GrabRequest(new VoucherCodeServiceImpl());
    	$grabRequest->voucher_id = $voucher->id;
    	$grabRequest->email = "masoud@gmail.com";
    	$this->assertInstanceOf("App\Http\Requests\GrabRequest", $grabRequest);
    	return $grabRequest;
    }

    /**
     * Pass grab request to grab method to assign the new voucher code to the user
     * @depends testCreateVoucher
     * @depends testGrabRequest
     */
    public function testGrab(Voucher $voucher, GrabRequest $grabRequest)
    {
    	$voucherCode = VoucherCode::grab($grabRequest);
    	$this->assertInstanceOf("App\Models\VoucherCode", $voucherCode);
        $this->assertEquals($voucher->name, $voucherCode->voucher->name);
        $this->assertEquals($grabRequest->code, $voucherCode->code);
        $this->assertEquals($grabRequest->email, $voucherCode->email);
        return $voucherCode;
    }

    /**
     * Create a UserVouchersList request
     */
    public function testUserVouchersListRequest()
    {
    	$userVouchersListRequest = new UserVouchersListRequest();
    	$userVouchersListRequest->email = "masoud@gmail.com";
    	$this->assertInstanceOf("App\Http\Requests\UserVouchersListRequest", $userVouchersListRequest);
    	return $userVouchersListRequest;
    }

    /**
     * Test UserVoucherCodesList to check the owner of the voucher code after grab process
     * @depends testGrab
     * @depends testUserVouchersListRequest
     */
    public function testUserVoucherCodesList(VoucherCode $voucherCode, UserVouchersListRequest $userVouchersListRequest)
    {
    	$this->assertEquals($userVouchersListRequest->email, $voucherCode->email);
    	$userVoucherCodesList = VoucherCode::getUserVouchersList($userVouchersListRequest);
    	$this->assertNotEmpty( $userVoucherCodesList->firstWhere("code", $voucherCode->code) );
    }

    /**
     * Create a redeem request
     * @depends testGrab
     */
    public function testRedeemRequest(VoucherCode $voucherCode)
    {
    	$redeemRequest = new RedeemRequest();
    	$redeemRequest->code = $voucherCode->code;
    	$redeemRequest->email = "masoud@gmail.com";
    	$this->assertInstanceOf("App\Http\Requests\RedeemRequest", $redeemRequest);
    	return $redeemRequest;
    }

    /**
     * Pass redeem request to redeem method to mark a valid voucher as redeemed and used.
     * @depends testRedeemRequest
     */
    public function testRedeem(RedeemRequest $redeemRequest)
    {	
    	$voucherCode = VoucherCode::redeem($redeemRequest);
    	$this->assertInstanceOf("App\Models\VoucherCode", $voucherCode);
        $this->assertNotNull($voucherCode->redeemed_at);
        return $voucherCode->voucher_id;
    }

    /**
     * Remove the test data
     * @depends testRedeem
     */
    public function testTearDownData(Int $voucherId)
    {
    	VoucherCode::where("voucher_id", $voucherId)->delete();
        $isVoucherDestroyed = Voucher::destroy($voucherId);
        $this->assertEquals(1, $isVoucherDestroyed);
    }
}
