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
        Schema::table('challenge_paths', function (Blueprint $table) {
            $table->enum('is_accessible', ['0', '1'])->default('1')->comment('0 -> Not accessable,1 -> Yes accessable');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('challenge_paths', function (Blueprint $table) {
            $table->dropColumn('is_accessible');
        });
    }
};
