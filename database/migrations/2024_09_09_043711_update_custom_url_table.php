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
        Schema::table('organizations', function (Blueprint $table) {
            DB::statement('ALTER TABLE `organizations` CHANGE `custom_url` `vanity_slug` VARCHAR(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL');
            DB::statement("ALTER TABLE `organization_customizations` ADD `custom_url` VARCHAR(255) NULL COMMENT 'custom url login slug' AFTER `enable_custom_login_and_registration`");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('organizations', function (Blueprint $table) {
            DB::statement('ALTER TABLE `organizations` CHANGE `vanity_slug` `custom_url` VARCHAR(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL');
            DB::statement('ALTER TABLE `organization_customizations` DROP `custom_url`');
        });
    }
};
