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
        Schema::table('desarrollos', function (Blueprint $table) {
            $table->enum('source_type', ['adara', 'naboo'])
                ->default('adara')
                ->after('id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('desarrollos', function (Blueprint $table) {
            $table->dropColumn('source_type');
        });
    }
};
