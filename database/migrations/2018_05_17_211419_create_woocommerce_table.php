<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWoocommerceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('woocommerce', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned()->index();
            $table->longtext('woocommerce_data')->nullable();
            $table->text('orders_data')->nullable();
            $table->text('credential')->nullable();
            $table->text('payment_gateway')->nullable();
            $table->text('tax_class')->nullable();
            $table->text('shipping_method')->nullable();
            $table->text('order_number')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('woocommerce');
    }
}
