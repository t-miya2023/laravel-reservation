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
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('detail');
            $table->string('price');
            $table->string('tax');
            $table->string('dinner_fee');
            $table->string('breakfast_fee');
            $table->integer('capacity');
            $table->string('bed_size');
            $table->string('smorking');
            $table->string('facility')->nullable();
            $table->string('amenities')->nullable();
            $table->string('img')->nullable();
            $table->boolean('status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};
