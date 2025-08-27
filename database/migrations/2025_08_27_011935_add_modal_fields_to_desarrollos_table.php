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
            $table->string('modal_color', 10)->nullable()->after('png_image'); // Hexadecimal (#ffffff)
            $table->string('modal_selector')->nullable()->after('modal_color'); // Selector string
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('desarrollos', function (Blueprint $table) {
            $table->dropColumn(['modal_color', 'modal_selector']);
        });
    }
};
