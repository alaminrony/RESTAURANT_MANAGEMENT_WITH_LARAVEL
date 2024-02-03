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
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('level');
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->unsignedBigInteger('discount_id')->nullable();


            $table->foreign('parent_id')->references('id')->on('categories');
            $table->foreign('discount_id')->references('id')->on('discounts');
            $table->timestamps();

            $table->index('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
