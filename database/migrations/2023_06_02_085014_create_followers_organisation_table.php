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
        Schema::create('followers_organisation', function (Blueprint $table) {
            $table->id();
            $table->string('organisation_id')->nullable();
            $table->string('user_id')->nullable();
            $table->enum('followers', ['0', '1'])->nullable()->comment("0=Unfollowers And `1=followers");
            $table->index(['organisation_id']);
            $table->index(['user_id']);
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
        Schema::dropIfExists('followers_organisation');
    }
};
