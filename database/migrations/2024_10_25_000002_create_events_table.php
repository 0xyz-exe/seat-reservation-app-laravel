<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('category')->default('Cinema'); // Cinema, Concert, Bus Transit, Theater
            $table->string('venue'); // e.g. Cinema Hall 1, Main Auditorium, Executive Bus 01
            $table->date('show_date');
            $table->string('start_time'); // e.g. 19:30
            $table->decimal('ticket_price', 10, 2)->default(12.00);
            $table->integer('total_rows')->default(5);
            $table->integer('seats_per_row')->default(8);
            $table->text('description')->nullable();
            $table->enum('status', ['active', 'completed', 'cancelled'])->default('active');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
