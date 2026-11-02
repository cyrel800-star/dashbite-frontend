<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('restaurants', function (Blueprint $table) {
            $table->text('profile_image_url')->nullable();
            $table->unsignedSmallInteger('prep_time_minutes')->default(20);
            $table->string('display_badge', 60)->nullable();
            $table->json('opening_hours')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('restaurants', function (Blueprint $table) {
            $table->dropColumn(['profile_image_url', 'prep_time_minutes', 'display_badge', 'opening_hours']);
        });
    }
};
