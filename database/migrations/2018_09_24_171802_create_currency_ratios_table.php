<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCurrencyRatiosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('currency_ratios', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('base_currency_id')->unsigned();
            $table->integer('target_currency_id')->unsigned();
            $table->decimal('ratio', 10, 4);
            $table->timestamps();

            $table->foreign('base_currency_id')->references('id')->on('currencies');
            $table->foreign('target_currency_id')->references('id')->on('currencies');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('currency_ratios');
    }
}
