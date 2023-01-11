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
        Schema::create('lab_tags', function (Blueprint $table) {
            $table->id();
            $table->integer("user_id");
            $table->integer("lab_id");
            $table->integer("tag");
            $table->timestamps();
            $table->softDeletes();
            $table->index("user_id");
            $table->index("lab_id");
            $table->index("tag");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lab_tags');
    }
};
