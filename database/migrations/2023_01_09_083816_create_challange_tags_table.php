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
        Schema::create('challange_tags', function (Blueprint $table) {
            $table->id();
            $table->integer("challange_id");
            $table->integer("user_id");
            $table->integer("tag");
            $table->timestamps();
            $table->softDeletes();
            $table->index("challange_id");
            $table->index("user_id");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('challange_tags');
    }
};
