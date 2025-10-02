<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('class_registrations', function (Blueprint $table) {
            $table->string('progress')->nullable()->after('status'); // Track user progress
            $table->text('comment')->nullable()->after('progress'); // Additional notes
            $table->text('workoutdiet')->nullable()->after('comment'); // Workout and diet plan
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('class_registrations', function (Blueprint $table) {
            $table->dropColumn(['progress', 'comment', 'workoutdiet']);
        });
    }
};
