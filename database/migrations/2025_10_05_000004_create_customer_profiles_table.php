<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customer_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone');
            $table->string('country')->nullable();
            $table->string('city')->nullable();
            $table->enum('gender', ['Male','Female'])->nullable();
            $table->integer('age')->nullable();
            $table->double('weight')->nullable();
            $table->text('chronic_pathologies')->nullable();
            $table->text('allergies')->nullable();
            $table->text('chronic_medications')->nullable();
            $table->text('medical_info')->nullable();
            $table->string('country_code')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('verification_code')->nullable();
            $table->boolean('is_verified')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customer_profiles');
    }
};