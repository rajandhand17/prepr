<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('job_titles', function (Blueprint $table) {
            $table->unsignedBigInteger('pathway_id')->nullable()->after('lightcast_id');
            $table->foreign('pathway_id')->references('id')->on('job_title_pathways')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('job_titles', function (Blueprint $table) {
            $table->dropForeign(['pathway_id']);
            $table->dropColumn('pathway_id');
        });
    }
};
