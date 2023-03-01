<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('organizations', function (Blueprint $table) {
            $table->string('language')->default('en');
            $table->integer('user_id');
            $table->string('slug');
            $table->string('description')->nullable();
            $table->string('cover_image')->nullable();
            $table->string('profile_image')->nullable();
            $table->string('website')->nullable();
            $table->text('about')->nullable();
            $table->integer('category')->nullable();
            $table->enum('status', ['0','1','2'])->comment("0 -> draft, 1-> published, 2-> deactivated")->default('1');
            $table->enum('is_verified', ['0','1'])->comment("0 -> not-verify, 1-> verify")->default('0');
            $table->integer('magnet_community_id')->nullable();
            $table->integer('total_employees')->nullable();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('organizations', function (Blueprint $table) {
            $table->dropColumn('language');
            $table->dropColumn('user_id');
            $table->dropColumn('name');
            $table->dropColumn('slug');
            $table->dropColumn('description');
            $table->dropColumn('cover_image');
            $table->dropColumn('profile_image');
            $table->dropColumn('website');
            $table->dropColumn('about');
            $table->dropColumn('category');
            $table->dropColumn('status');
            $table->dropColumn('magnet_community_id');
            $table->dropColumn('total_employees');
           
        });
    }
};
