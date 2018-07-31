<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVouchersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vouchers', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->charset('utf8');
            $table->smallInteger('percentage_discount')->comment('fixed percentage discount');
            $table->unsignedTinyInteger('quota')->comment('number of available promotion');
            $table->smallInteger('validity_period')->comment('length of validity period (day)');
            $table->timestamps();
        });
        DB::update("ALTER TABLE vouchers AUTO_INCREMENT = 1000;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vouchers');
    }
}
