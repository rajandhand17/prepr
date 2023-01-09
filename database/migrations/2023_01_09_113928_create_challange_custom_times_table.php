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
        Schema::create('challange_custom_times', function (Blueprint $table) {
            $table->id();
            $table->integer("challenge_id");
            $table->string("title")->nullable();
            $table->dateTime('date')->nullable()->default(null);
            $table->string("description")->nullable();
            $table->enum("schedule_announcement",['no', 'yes'])->nullable()->comment("select schedule announcement yes/no");
            $table->integer("custom_date_number")->nullable()->default(null);
            $table->string("custom_date_duration")->nullable()->default(null);
            $table->timestamps();
            $table->softDeletes();
            $table->index("challenge_id");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('challange_custom_times');
    }
};
