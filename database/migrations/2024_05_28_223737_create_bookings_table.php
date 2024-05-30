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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->unsignedBigInteger('train_id');
            $table->foreign('train_id')->references('id')->on('trains')->onDelete('cascade');
            $table->string('departure_city');
            $table->string('arrival_city');
            $table->date('travel_date');
            $table->time('departure_time');
            $table->time('arrival_time');
            $table->string('seat_number');
            $table->string('ticket_number');
            // $table->unsignedBigInteger('route_id');
            // $table->foreign('route_id')->references('id')->on('routes')->onDelete('cascade');
            // $table->string('departure_city');
            // $table->string('arrival_city');
            // $table->date('travel_date');
            // $table->time('departure_time');
            // $table->string('seat_number');
            // $table->string('status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
