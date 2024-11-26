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
        Schema::create('test_requests', function (Blueprint $table) {
            $table->uuid('id')->primary(); // UUID as primary key
            $table->string('name');
            $table->text('note')->nullable();
            $table->string('status');
            $table->boolean('is_paid')->default(false);
            $table->foreignUuid('doctor_id')->constrained('users')->nullOnDelete();
            $table->foreignUuid('patient_id')->constrained('patients')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('test_requests');
    }
};
