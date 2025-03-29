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
        Schema::table('genrated_emails', function (Blueprint $table) {
            $table->unsignedBigInteger('language_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('genrated_emails', function (Blueprint $table) {
            $table->dropColumn('language_id');
        });
    }
};
