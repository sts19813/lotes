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
        $table->string('color_primario')->nullable();
        $table->string('color_acento')->nullable();
        $table->integer('financing_months')->nullable();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('desarrollos', function (Blueprint $table) {
            $table->dropColumn(['color_primario', 'color_acento', 'financing_months']);
        });
    }
};
