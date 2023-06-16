<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('organizations', function (Blueprint $table) {
            $table->string('language')->default('en')->after('id');
            $table->integer('user_id')->after('language');
            $table->string('slug')->after('display_name');
            $table->string('cover_image')->nullable()->after('description');
            $table->string('profile_image')->nullable()->after('cover_image');
            $table->string('website')->nullable()->after('profile_image');
            $table->text('about')->nullable()->after('website');
            $table->unsignedBigInteger('category')->nullable()->after('about');
            $table->enum('status', ['0', '1', '2'])->comment('0 -> draft, 1-> published, 2-> deactivated')->default('1')->after('category');
            $table->enum('is_verified', ['0', '1'])->comment('0 -> not-verify, 1-> verify')->default('0')->after('status');
            $table->integer('magnet_community_id')->nullable()->after('is_verified');
            $table->integer('total_employees')->nullable()->after('magnet_community_id');
            $table->text('description')->nullable()->change();
            $table->softDeletes();
            $table->foreign('category')->references('id')->on('categories')->onDelete('cascade');
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
