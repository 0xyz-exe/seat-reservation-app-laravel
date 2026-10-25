<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('seats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->string('seat_number', 10); // e.g. A1, A2
            $table->string('row_letter', 5); // e.g. A
            $table->integer('column_number'); // e.g. 1
            $table->enum('seat_type', ['standard', 'vip'])->default('standard');
            $table->decimal('price', 10, 2);
            $table->timestamps();

            $table->unique(['event_id', 'seat_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('seats');
    }
};
