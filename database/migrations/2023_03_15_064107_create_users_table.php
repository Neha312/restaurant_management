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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('role_id')->unsigned()->nullable();
            $table->string('first_name', 30)->nullable();
            $table->string('last_name', 30)->nullable();
            $table->date('joining_date');
            $table->date('ending_date')->nullable();
            $table->string('email')->unique();
            $table->string('password', 251);
            $table->string('address1', 50)->nullable();
            $table->string('address2', 50)->nullable();
            $table->string('zip_code', 6)->nullable();
            $table->string('phone', 10)->nullable();
            $table->char('total_leave', 2)->nullable();
            $table->char('used_leave', 2)->nullable();
            $table->timestamps();
            $table->char('created_by')->nullable();
            $table->char('updated_by')->nullable();
            $table->char('deleted_by')->nullable();

            $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
