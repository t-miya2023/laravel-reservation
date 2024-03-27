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
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->date('checkin_date');
            $table->time('checkin_time');
            $table->date('checkout_date');
            $table->integer('total');
            $table->integer('number_of_men');
            $table->integer('number_of_women');
            $table->string('dinner');
            $table->string('breakfast');
            $table->string('payment_info');
            $table->string('reservation_fee');
            $table->text('remarks_column')->nullable();
            $table->boolean('payment_status');
            $table->string('payment_number')->nullable();
            $table->string('creditcard_company')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
