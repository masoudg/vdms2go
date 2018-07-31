<?php

use Illuminate\Database\Seeder;

class VouchersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(App\Models\Voucher::class, 5)->create()->each(function ($v) {
	        $v->voucherCodes()->save(factory(App\Models\VoucherCode::class)->make());
	    });
    }
}
