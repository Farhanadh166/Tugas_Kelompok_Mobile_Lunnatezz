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
        if (!Schema::hasColumn('users', 'photo_url')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('photo_url')->nullable()->after('peran');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('users', 'photo_url')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('photo_url');
            });
        }
    }
}; 