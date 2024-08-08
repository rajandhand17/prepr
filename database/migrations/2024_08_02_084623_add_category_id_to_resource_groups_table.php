<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('resource_groups', function (Blueprint $table) {
            $table->unsignedBigInteger('category_id')->after('organization_id')->nullable();
            // Add the foreign key constraint
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('resource_groups', function (Blueprint $table) {
            $table->dropColumn('category_id');
        });
    }
};
