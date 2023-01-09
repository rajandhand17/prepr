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
        Schema::create('resource_datas', function (Blueprint $table) {
            $table->id();
            $table->bigInteger("admin_challenge_id");
            $table->bigInteger("resource_datas_id");
            $table->timestamps();
            $table->softDeletes();
            $table->index("admin_challenge_id");
            $table->index("resource_datas_id");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('resource_datas');
    }
};
