<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_id')->unique();

            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('payment_method_id')->nullable()->constrained('payment_methods')->onDelete('set null');
            $table->foreignId('doctor_id')->nullable()->constrained('doctors')->onDelete('set null');

            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('USD');
            $table->enum('payment_status', [
                'pending',
                'processing',
                'completed',
                'succeeded',
                'requires_action',
                'requires_payment_method',
                'failed',
                'refunded',
                'canceled'
            ])->default('pending');
            $table->string('stripe_payment_intent_id')->nullable();

            $table->string('transaction_type')->nullable();
            $table->string('service_type')->nullable();
            $table->string('doctor_name')->nullable();
            $table->date('appointment_date')->nullable();
            $table->time('appointment_time')->nullable();
            $table->string('location')->nullable();

            $table->text('notes')->nullable();
            $table->json('metadata')->nullable();

            $table->timestamp('paid_at')->nullable();
            $table->timestamp('failed_at')->nullable();
            $table->timestamp('refunded_at')->nullable();

            $table->timestamps();

            $table->index('stripe_payment_intent_id');
            $table->index(['user_id', 'payment_status']);
            $table->index('doctor_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};