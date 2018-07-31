<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\QueryException;
use App\Exceptions\NotFoundException;
use App\Exceptions\NoQuotaException;

class Voucher extends Model 
{
    protected $table = 'vouchers';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        "name", "percentage_discount", "quota", "validity_period"
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        "updated_at", "created_at", "validity_period"
    ];

    /**
     * Get the voucher codes that belongs to the voucher.
     */
    public function voucherCodes()
    {
        return $this->hasMany('App\Models\VoucherCode');
    }

    /**
     * The query that returns vouchers list.
     *
     */
    public function scopeGetAllVouchersList(Builder $query)
    {
        $vouchersList = $query->orderBy("id","desc")->get();

        if( count($vouchersList)<1 )
            throw new NotFoundException("No voucher found.");

        return $vouchersList;
    }

    /**
     * The query that deducts the voucher quota and returns the voucher details
     *
     */
    public function scopeDeductVoucherQuota(Builder $query, Int $id)
    {
        try {
            $query->where("id", $id)->decrement("quota", 1);
            return $query->select("id", "name", "percentage_discount", "validity_period")->find($id);
        } catch (QueryException $e) {
            throw new NoQuotaException("The promotion for the selected voucher has ended.");
        }
    }
}
