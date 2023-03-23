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
        Schema::create('restaurant_pictures', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('restaurant_id')->unsigned()->nullable();
            $table->string('picture')->nullable();
            $table->enum('type', ['M', 'O'])->comment('M: Menu,O: Other')->default('M');

            $table->foreign('restaurant_id')->references('id')->on('restaurants')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('restaurant_pictures');
    }
};
