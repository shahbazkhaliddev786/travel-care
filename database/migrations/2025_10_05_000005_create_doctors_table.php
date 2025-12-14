<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('doctors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('country_code')->nullable();
            $table->string('professional_id')->nullable();
            $table->string('license_scan')->nullable();
            $table->text('address')->nullable();
            $table->text('specialization')->nullable();
            $table->decimal('consultation_fee', 8, 2)->nullable();
            $table->boolean('is_verified')->default(false);
            $table->boolean('can_video_consult')->default(false);
            $table->string('rejection_reason')->nullable();
            $table->enum('type', ['Doctor','Laboratory'])->default('Doctor');
            $table->string('profile_image')->nullable();
            $table->decimal('messaging_fee', 8, 2)->nullable();
            $table->decimal('video_call_fee', 8, 2)->nullable();
            $table->decimal('house_visit_fee', 8, 2)->nullable();
            $table->decimal('voice_call_fee', 8, 2)->nullable();
            $table->time('working_hours_from')->default('08:00:00');
            $table->time('working_hours_to')->default('19:00:00');
            $table->json('working_days')->default('["Monday","Tuesday","Wednesday","Thursday","Friday"]');
            $table->string('city')->nullable();
            $table->integer('years_of_experience')->nullable();
            $table->string('working_location')->nullable();
            $table->text('description')->nullable();
            $table->json('payment_methods')->nullable();
            $table->string('paypal_email')->nullable();
            $table->json('gallery_images')->nullable();
            $table->timestamps();
        });

        if (Schema::hasColumn('doctors', 'bio')) {
            Schema::table('doctors', function (Blueprint $table) {
                $table->dropColumn('bio');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('doctors');
    }
};