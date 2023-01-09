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
        Schema::create('challange_assessments', function (Blueprint $table) {
            $table->id();
            $table->integer('challenge_id');
            $table->enum('assessment_type', ['open', 'closed', 'none']);
            $table->enum('visibility', ['users', 'hidden', 'none']);
            $table->text("members")->nullable()->default(null);
            $table->text("guidline")->nullable()->default(null);
            $table->text("attachment")->nullable()->default(null);
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
        Schema::dropIfExists('challange_assessments');
    }
};
