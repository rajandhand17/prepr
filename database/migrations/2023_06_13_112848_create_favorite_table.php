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
        Schema::create('favorite', function (Blueprint $table) {
            $table->id();
            $table->integer("user_id");
            $table->integer("refence_id")->nullable();
            $table->enum("type",['0','1','2','3','4','5'])->comment("0 = lab , 1=project,2=user, 3=challange, 4=challenge_group,5=lab_group");
            $table->enum("action",["is_favorite","is_like","is_follow"])->comment("0 = is_favorite , 1=is_like,2=is_follow");
            $table->enum("status",["0","1"])->comment("0 for yes, 1 for no")->default(0);
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
        Schema::dropIfExists('favorite');
    }
};
