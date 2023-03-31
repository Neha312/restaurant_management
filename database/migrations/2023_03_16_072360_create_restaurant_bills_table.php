<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('restaurant_bills', function (Blueprint $table) {
            $table->id();
            $table->string('bill_number', 6)->unique();
            $table->bigInteger('order_id')->unsigned()->nullable();
            $table->bigInteger('restaurant_id')->unsigned()->nullable();
            $table->bigInteger('vendor_id')->unsigned()->nullable();
            $table->bigInteger('stock_type_id')->unsigned()->nullable();
            $table->string('total_amount', 10)->nullable();
            $table->string('tax', 10)->nullable();
            $table->date('due_date');
            $table->timestamps();
            $table->char('created_by')->nullable();
            $table->char('updated_by')->nullable();
            $table->char('deleted_by')->nullable();

            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            $table->foreign('restaurant_id')->references('id')->on('restaurants')->onDelete('cascade');
            $table->foreign('stock_type_id')->references('id')->on('stock_types')->onDelete('cascade');
            $table->foreign('vendor_id')->references('id')->on('vendors')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('restaurant_bills');
    }
};
