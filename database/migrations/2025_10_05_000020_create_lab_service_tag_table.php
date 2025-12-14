<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lab_service_tag', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lab_service_id')->constrained('lab_services')->onDelete('cascade');
            $table->foreignId('tag_id')->constrained('tags')->onDelete('cascade');
            $table->timestamps();

            $table->unique(['lab_service_id', 'tag_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lab_service_tag');
    }
};