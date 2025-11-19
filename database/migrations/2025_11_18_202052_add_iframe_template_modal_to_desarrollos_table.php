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
            $table->string('iframe_template_modal')->nullable()->after('png_image');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('desarrollos', function (Blueprint $table) {
            $table->dropColumn('iframe_template_modal');
        });
    }
};
