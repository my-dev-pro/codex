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
        Schema::create('patients', function (Blueprint $table) {
            $table->uuid('id')->primary(); // UUID primary key
            $table->string('first_name', 255); // First name
            $table->string('middle_name', 255)->nullable(); // Optional middle name
            $table->string('last_name', 255); // Last name
            $table->string('telephone', 15)->nullable(); // Optional telephone
            $table->string('mobile', 15)->nullable(); // Optional mobile
            $table->string('email', 255)->nullable(); // Optional email
            $table->enum('gender', ['male', 'female', 'other']); // Gender enum
            $table->text('address')->nullable(); // Optional address
            $table->date('date_of_birth'); // Date of birth
            $table->string('nationality')->nullable();
            $table->string('national_id', 16)->unique(); // Unique national ID
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patients');
    }
};
