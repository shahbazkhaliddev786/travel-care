<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hospitals', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type')->nullable();
            $table->string('email')->unique();
            $table->string('country_code');
            $table->string('phone');
            $table->string('website')->nullable();
            $table->string('logo')->nullable();
            $table->string('city');
            $table->string('state')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('address');
            $table->text('specialties')->nullable();
            $table->text('facilities')->nullable();
            $table->integer('bed_count')->nullable();
            $table->boolean('emergency_services')->default(false);
            $table->boolean('pharmacy')->default(false);
            $table->time('operating_hours_from')->nullable();
            $table->time('operating_hours_to')->nullable();
            $table->json('operating_days')->nullable();
            $table->text('description')->nullable();
            $table->string('country')->nullable();
            $table->string('professional_id')->nullable();
            $table->string('profile_image')->nullable();
            $table->string('license_scan')->nullable();
            $table->tinyInteger('is_verified')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hospitals');
    }
};