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
        Schema::table('dashboard_layouts', function (Blueprint $table) {
            // Drop the columns you want to remove
            $table->dropColumn(['position_x', 'position_y']);

            // Add the new column
            $table->integer('position_index')->after('is_active')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dashboard_layouts', function (Blueprint $table) {
            $table->integer('position_x')->comment('card on the X-axis')->nullable();
            $table->integer('position_y')->comment('card on the Y-axis')->nullable();
            $table->dropColumn('position_index');
        });
    }
};
