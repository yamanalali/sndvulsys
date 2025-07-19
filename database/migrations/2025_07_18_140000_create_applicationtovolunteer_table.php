<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Create applicationtovolunteer table
 * 
 * This table stores volunteer applications with all data in JSON format
 * The details column contains:
 * - full_name, last_name, email, phone
 * - national_id, birth_date, gender
 * - address, city, country
 * - education_level, occupation, skills
 * - motivation, previous_experience
 * - preferred_area, availability
 * - has_previous_volunteering
 * - preferred_organization_type
 * - emergency_contact_name, emergency_contact_phone
 */

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('applicationtovolunteer', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->json('details')->comment('Contains all volunteer application data as JSON');
            $table->enum('status', ['pending', 'approved', 'rejected', 'withdrawn'])->default('pending');
            $table->timestamp('reviewed_at')->nullable();
            $table->unsignedBigInteger('reviewed_by')->nullable();
            $table->text('admin_notes')->nullable();
            $table->timestamps();

            $table->foreign('reviewed_by')->references('id')->on('users')->onDelete('set null');
            
            // Add indexes for better performance
            $table->index('status');
            $table->index('created_at');
            $table->index('reviewed_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('applicationtovolunteer');
    }
}; 