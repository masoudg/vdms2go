<?php

use App\Services\VoucherCodeServiceImpl;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

$factory->define(App\Models\Voucher::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->name,
        'percentage_discount' => $faker->randomNumber(2),
        'validity_period' => $faker->randomNumber(2),
        'quota' => $faker->randomNumber(2)
    ];
});

$factory->define(App\Models\VoucherCode::class, function (Faker\Generator $faker) {
	$voucherCodeService = new VoucherCodeServiceImpl();
    return [
        'voucher_id' => $faker->randomNumber(4),
        'code' => $voucherCodeService->generate(),
        'email' => $faker->email,
        'is_available' => $faker->randomNumber(2),
        'expire_at' => $faker->dateTimeBetween('tomorrow', '1 year') 
    ];
});

$factory->define(App\Http\Requests\GrabRequest::class, function (Faker\Generator $faker) {
    return [
        'email' => $faker->email,
        'voucher_id' => $faker->randomNumber(3)
    ];
});
