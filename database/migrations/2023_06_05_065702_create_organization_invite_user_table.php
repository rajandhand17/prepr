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
        Schema::create('organization_invite_user', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('organisation_id')->nullable();
            $table->integer('user_id')->nullable();
            $table->string('role')->nullable();
            $table->string('email')->nullable();
            $table->enum('status', ['0', '1'])->nullable()->default('0');
            $table->timestamps();
            $table->softDeletes();
            $table->index(['organisation_id']);
            $table->index(['user_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('organization_invite_user');
    }
};
