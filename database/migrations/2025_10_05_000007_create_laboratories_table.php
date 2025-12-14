<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('laboratories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone');
            $table->string('country_code')->default('+1');
            $table->string('license_number')->nullable();
            $table->string('license_scan')->nullable();
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('country')->nullable();
            $table->string('specialization')->nullable();
            $table->text('bio')->nullable();
            $table->decimal('consultation_fee', 8, 2)->nullable();
            $table->decimal('messaging_fee', 8, 2)->nullable();
            $table->decimal('video_call_fee', 8, 2)->nullable();
            $table->decimal('house_visit_fee', 8, 2)->nullable();
            $table->decimal('voice_call_fee', 8, 2)->nullable();
            $table->time('working_hours_from')->default('08:00:00');
            $table->time('working_hours_to')->default('19:00:00');
            $table->json('working_days')->default('["Monday","Tuesday","Wednesday","Thursday","Friday"]');
            $table->integer('years_of_experience')->nullable();
            $table->string('working_location')->nullable();
            $table->text('description')->nullable();
            $table->json('payment_methods')->nullable();
            $table->string('paypal_email')->nullable();
            $table->json('gallery_images')->nullable();
            $table->string('profile_image')->nullable();
            $table->boolean('is_verified')->default(false);
            $table->boolean('can_video_consult')->default(false);
            $table->timestamp('verification_date')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->boolean('is_available')->default(true);
            $table->decimal('rating', 3, 2)->default(0.00);
            $table->integer('total_jobs')->default(0);
            $table->string('bank_account_number')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('bank_routing_number')->nullable();
            $table->string('bank_account_holder_name')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('laboratories');
    }
};