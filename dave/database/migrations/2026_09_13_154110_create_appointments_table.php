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
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Sino ang Customer/Patient
            $table->foreignId('service_id')->constrained()->onDelete('cascade'); // Anong Dental Service
            $table->dateTime('appointment_date'); // Petsa at Oras ng Checkup
            $table->string('status')->default('Pending'); // Pending, Approved, Completed, Cancelled
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
