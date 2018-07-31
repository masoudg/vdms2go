<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Http\Requests\GrabRequest;
use App\Http\Requests\RedeemRequest;
use App\Http\Requests\UserVouchersListRequest;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;
use App\Exceptions\InvalidVoucherCodeException;
use App\Exceptions\NotFoundException;

class VoucherCode extends Model 
{
    protected $table = 'voucher_codes';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        "voucher_id", "code", "email", "is_available", "redeemed_at", "expire_at"
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        "updated_at", "created_at", "id", "voucher_id", "is_available"
    ];

    /**
     * Get the voucher that owns the voucher code.
     */
    public function voucher()
    {
        return $this->belongsTo('App\Models\Voucher');
    }

    /**
     * The query that checks voucher's quota and stores an on the fly voucher code
     * which can be redeemed later by receiver (owner) of the voucher code.
     *
     */
    public function scopeGrab(Builder $query, GrabRequest $request) 
    {
        $voucher = Voucher::DeductVoucherQuota($request->voucher_id);
        $expireAt = Carbon::now()->addDays($voucher->validity_period)->toDateTimeString();
        return $query->create(["email" => $request->email, 
                            "voucher_id" => $request->voucher_id, 
                            "code" => $request->code,
                            "expire_at" => $expireAt ])
                            ->voucher()
                            ->associate($voucher);
    }

    /**
     * The query that marks a valid voucher as redeemed and used.
     *
     */
    public function scopeRedeem(Builder $query, RedeemRequest $request) 
    {
        $voucherCode = $query->with( ['voucher' => function($query){ $query->select("id", "name", "percentage_discount"); }] )
                        ->where("email", $request->email)
                        ->where("code", $request->code)
                        ->where("expire_at", ">", Carbon::now()->toDateTimeString())
                        ->where("is_available", true)
                        ->first();

        if( empty($voucherCode) )
            throw new InvalidVoucherCodeException("The voucher code is invalid/expired.");

        $voucherCode->redeemed_at = Carbon::now()->toDateTimeString();
        $voucherCode->is_available = false;
        $voucherCode->save();

        return $voucherCode;
    }

    /**
     * The query that returns user's vouchers list.
     *
     */
    public function scopeGetUserVouchersList(Builder $query, UserVouchersListRequest $request)
    {
        $vouchersList = $query->with( ['voucher' => function($query){ $query->select("id", "name", "percentage_discount"); }] )
                        ->where("email", $request->email)
                        ->where("expire_at", ">", Carbon::now()->toDateTimeString())
                        ->where("is_available", true)
                        ->get();

        if( count($vouchersList)<1 )
            throw new NotFoundException("No voucher found.");

        return $vouchersList;
    }

    /**
     * The query that returns voucher codes list.
     *
     */
    public function scopeGetAllCodesList(Builder $query)
    {
        $codesList = $query->orderBy("id","desc")->get();

        if( count($codesList)<1 )
            throw new NotFoundException("No voucher code found.");

        return $codesList;
    }
}
