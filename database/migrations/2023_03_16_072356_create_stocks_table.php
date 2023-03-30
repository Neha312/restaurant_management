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
        Schema::create('stocks', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('stock_type_id')->unsigned()->nullable();
            $table->string('name', 10)->nullable();
            $table->char('price')->nullable();
            $table->char('quantity')->nullable();
            $table->char('tax')->nullable();
            $table->date('manufacture_date');
            $table->date('expired_date');
            $table->boolean('is_available')->default(true);
            $table->timestamps();
            $table->char('created_by')->nullable();
            $table->char('updated_by')->nullable();
            $table->char('deleted_by')->nullable();

            $table->foreign('stock_type_id')->references('id')->on('stock_types')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stocks');
    }
};
