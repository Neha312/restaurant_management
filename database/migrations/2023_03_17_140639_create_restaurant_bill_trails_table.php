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
        Schema::create('restaurant_bill_trails', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('restaurant_bill_id')->unsigned()->nullable();
            $table->enum('status', ['PN', 'P'])->comment('PN: Pending,P: Paid')->default('PN');
            $table->timestamps();
            $table->char('created_by')->nullable();
            $table->char('updated_by')->nullable();
            $table->char('deleted_by')->nullable();

            $table->foreign('restaurant_bill_id')->references('id')->on('restaurant_bills')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('restaurant_bill_trails');
    }
};
