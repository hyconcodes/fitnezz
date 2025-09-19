<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update role names
        DB::table('roles')->where('name', 'patient')->update(['name' => 'student']);
        DB::table('roles')->whereIn('name', ['doctor', 'nurse'])->update(['name' => 'trainer']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert role names
        DB::table('roles')->where('name', 'student')->update(['name' => 'patient']);
        DB::table('roles')->where('name', 'trainer')->update(['name' => 'doctor']);
    }
};
