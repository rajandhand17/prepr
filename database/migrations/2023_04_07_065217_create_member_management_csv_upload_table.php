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
        Schema::create('member_management_csv_upload', function (Blueprint $table) {
            $table->bigInteger('inviter_id')->comment('if join request auto add the component user id');
            $table->string('csv');
            $table->string('is_processed');
            $table->string('process_status');
            $table->integer('total_count');
            $table->integer('failure_count');
            $table->string('processed_csv');
            $table->timestamps();
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
        Schema::dropIfExists('member_management_csv_upload');
    }
};
