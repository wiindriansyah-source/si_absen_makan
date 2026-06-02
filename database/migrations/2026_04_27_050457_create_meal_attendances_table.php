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
        Schema::create('meal_attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->foreignId('department_id')->nullable()->constrained(); // Denormalisasi untuk mempermudah reporting
            $table->foreignId('visitor_id')->nullable()->constrained();
            $table->string('meal_type'); // Kantin / Kotakan
            $table->string('meal_time'); // Pagi / Siang / Malam / Tengah Malam

            $table->string('satisfaction'); // Puas / Tidak Puas
            $table->text('feedback')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('meal_attendances');
    }
};
