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
            $table->string('path_logo')->nullable()->after('png_image');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        
        Schema::table('desarrollos', function (Blueprint $table) {
            $table->dropColumn('path_logo');
        });
    }
};
