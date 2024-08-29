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
        Schema::table('member_management', function (Blueprint $table) {
            $table->enum('is_associated_member', ['yes', 'no'])->default('no')->after('email_body');
            $table->enum('associated_component', ['project', 'organization', 'lab', 'lab_program', 'challenge', 'challenge_path', 'resource_module', 'resource_collection', 'resource_group'])->after('is_associated_member')->nullable();
            $table->unsignedBigInteger('associated_component_id')->after('associated_component')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('member_management', function (Blueprint $table) {
            $table->dropColumn('is_associated_member');
            $table->dropColumn('associated_component');
            $table->dropColumn('associated_component_id');
        });
    }
};
