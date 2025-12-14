<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('translators', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone');
            $table->string('country_code')->default('+1');
            $table->string('country')->nullable();
            $table->string('city')->nullable();
            $table->text('address')->nullable();
            $table->json('languages')->nullable();
            $table->json('specializations')->nullable();
            $table->text('bio')->nullable();
            $table->decimal('hourly_rate', 8, 2)->nullable();
            $table->json('availability')->nullable();
            $table->integer('experience_years')->nullable();
            $table->string('profile_image')->nullable();
            $table->boolean('is_verified')->default(false);
            $table->timestamp('verification_date')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->boolean('is_available')->default(true);
            $table->decimal('rating', 3, 2)->default(0.00);
            $table->integer('total_jobs')->default(0);
            $table->string('paypal_email')->nullable();
            $table->string('bank_account_number')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('bank_routing_number')->nullable();
            $table->string('bank_account_holder_name')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('translators');
    }
};