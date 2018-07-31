<?php

namespace App\Http\Controllers;

use App\Models\Voucher;
use App\Exceptions\NotFoundException;

class VoucherController extends Controller
{
    /**
     * Service description
     * An action that returns the service description
     * 
     * The configuration in the <code>routes</code> file means that this method
     * will be called when the application receives a <code>GET</code> request
     * with a path of <code>/</code>.
     */
    public function index()
    {
        return response()->json([
                                "service_name" => "VDMS",
                                "build_date" => "July 2018",
                                "version" => "1.0",
                                "description" => "Voucher Distribution Microservice (VDMS)"
                            ]);
    }

    /**
     * List of all vouchers
     * An action that returns all vouchers list
     * 
     * The configuration in the <code>routes</code> file means that this method
     * will be called when the application receives a <code>POST</code> request
     * with a path of <code>/voucher/index</code>.
     */
    public function getAllVouchersList()
    {
        try {
            $vouchersList = Voucher::getAllVouchersList();
            return response()->json(["message" => "There are ".count($vouchersList)." available voucher(s).", 
                                    "data"=>$vouchersList]);
        } catch (NotFoundException $e) {
            return response()->json(["message" => $e->getMessage()]);
        }
    }
}
