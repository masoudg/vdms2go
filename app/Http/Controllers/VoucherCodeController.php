<?php

namespace App\Http\Controllers;

use App\Models\VoucherCode;
use App\Http\Requests\GrabRequest;
use App\Http\Requests\RedeemRequest;
use App\Http\Requests\UserVouchersListRequest;
use Illuminate\Database\QueryException;
use App\Exceptions\NotFoundException;
use App\Exceptions\InvalidVoucherCodeException;
use App\Exceptions\NoQuotaException;
use \Log;

class VoucherCodeController extends Controller
{
    /**
     * List of all voucher codes
     * An action that returns all voucher codes list
     * 
     * The configuration in the <code>routes</code> file means that this method
     * will be called when the application receives a <code>POST</code> request
     * with a path of <code>/voucher/code/index</code>.
     */
    public function getAllCodesList()
    {
        try {
            $codesList = VoucherCode::getAllCodesList();
            return response()->json(['message' => 'There are '.count($codesList).' voucher code(s).', 'data'=>$codesList]);
        } catch (NotFoundException $e) {
            return response()->json(['message' => $e->getMessage()]);
        }
    }

    /**
     * List of user's voucher codes
     * An action that returns user's voucher codes list
     * 
     * The configuration in the <code>routes</code> file means that this method
     * will be called when the application receives a <code>POST</code> request
     * with a path of <code>/voucher/user/index</code>.
     */
    public function getUserVoucherCodesList(UserVouchersListRequest $request)
    {
        try {
            $voucherCodesList = VoucherCode::getUserVouchersList($request);
            return response()->json(['message' => 'You have '.count($voucherCodesList).' available voucher(s).', 'data'=>$voucherCodesList]);
        } catch (NotFoundException $e) {
            return response()->json(['message' => $e->getMessage()]);
        }
    }

    /**
     * Assign a voucher to user
	 * An action that generates an on the fly voucher code into the system
	 * which can be redeemed later by receiver (owner) of the voucher code.
	 * 
	 * The configuration in the <code>routes</code> means that this method
	 * will be called when the application receives a <code>POST</code> request
	 * with a path of <code>/voucher/grab</code>.
	 */
    public function grab(GrabRequest $request)
    {
        try {
            $voucherCode = VoucherCode::grab($request);
            return response()->json(['message' => 'Congratulations! You just unlocked a voucher code.', 'data'=>$voucherCode]);
        } catch (NoQuotaException $e) {
            return response()->json(['message' => $e->getMessage()]);
        } catch (QueryException $e) {
            Log::error('Internal error: failed to grab due to QueryException.', 
                        ["email"=>$request->email, 
                         "voucher_id"=>$request->voucher_id, 
                         "code"=>$request->code,
                         "error_message"=>$e->getMessage()]);
            return response()->json(['message' => 'Internal error, please try again later.'], 500);
        }
    }

    /**
     * Redeem a voucher
     * An action that marks a voucher as redeemed
     * 
     * The configuration in the <code>routes</code> file means that this method
     * will be called when the application receives a <code>POST</code> request
     * with a path of <code>/voucher/redeem</code>.
     */
    public function redeem(RedeemRequest $request)
    {
        try {
            $voucherCode = VoucherCode::redeem($request);
            return response()->json(['message' => 'Congratulations! You just redeemed your voucher.', 'data'=>$voucherCode]);
        } catch (InvalidVoucherCodeException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }
}
