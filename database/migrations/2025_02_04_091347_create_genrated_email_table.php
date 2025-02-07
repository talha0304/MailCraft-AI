<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('genrated_email', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('recipient_email')->nullable();
            $table->string('cc')->nullable();
            $table->string('email_subject')->nullable();
            $table->string('description')->nullable();
            $table->string('content')->nullable();
            $table->string('status')->nullable();
            $table->string('ai_model_used')->nullable();
            $table->dateTime('genrated_at')->nullable();
            $table->dateTime('sent_at')->nullable();
            $table->foreignId('prefence_id')->constrained('prefences')->cascadeOnDelete();
            $table->foreignId('template_id')->constrained('templates')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('genrated_email');
    }
};
