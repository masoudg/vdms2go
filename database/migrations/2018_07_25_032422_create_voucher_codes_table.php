<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVoucherCodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('voucher_codes', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('voucher_id');
            $table->string('code', 9)->unique()->comment('unique voucher code');
            $table->string('email')->index()->comment('email of the user who got the voucher');
            $table->boolean('is_available')->default(true)->comment('true if voucher is not used nor expired');
            $table->dateTime('redeemed_at')->nullable()->comment('date and time when user redeemed the voucher');
            $table->dateTime('expire_at')->nullable()->comment('date and time when voucher expires');
            $table->timestamps();

            $table->foreign('voucher_id')->references('id')->on('vouchers');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('voucher_codes');
    }
}
