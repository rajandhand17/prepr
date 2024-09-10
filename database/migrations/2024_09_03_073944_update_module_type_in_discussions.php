<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('discussions', function (Blueprint $table) {
            DB::statement("ALTER TABLE discussions MODIFY COLUMN module_type ENUM('0', '1', '2', '3') COMMENT '0-> labs,1-> project,2-> challenge, 3-> challenge-path'");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('discussions', function (Blueprint $table) {
            DB::statement("ALTER TABLE discussions MODIFY COLUMN module_type ENUM('0', '1', '2') COMMENT '0-> labs,1-> project,2-> challenge'");
        });
    }
};
