<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('city_id');
            $table->unsignedBigInteger('province_id');
            $table->string('invoice');
            $table->string('courier');
            $table->string('courier_service');
            $table->bigInteger('courier_cost');
            $table->integer('weight');
            $table->string('name');
            $table->string('phone');
            $table->text('address');
            $table->enum('status', ['pending', 'success', 'expired', 'failed']);
            $table->bigInteger('grand_total');
            $table->string('snap_token')->nullable();
            $table->timestamps();

            $table->foreign('customer_id')->references('id')->on('customers');
            $table->foreign('city_id')->references('id')->on('cities');
            $table->foreign('province_id')->references('id')->on('provinces');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('invoices');
    }
}
