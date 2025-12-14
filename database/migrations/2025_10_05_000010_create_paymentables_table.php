<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('paymentables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_method_id')->constrained()->onDelete('cascade');
            $table->morphs('paymentable');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('paymentables');
    }
};