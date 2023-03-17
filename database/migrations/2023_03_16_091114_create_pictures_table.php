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
        Schema::create('pictures', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('restaurant_picture_id')->unsigned()->nullable();
            $table->string('picture')->nullable();
            $table->enum('type', ['M', 'O'])->comment('M: Menu,O: Other')->default('M');
            $table->timestamps();
            $table->foreign('restaurant_picture_id')->references('id')->on('restaurant_pictures')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pictures');
    }
};
