<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('email_templates', function (Blueprint $table) {
            $table->id();
            $table->enum('template_type', ['0'])->comment('0 -> invitation');
            $table->enum('module_type', ['0', '1', '2', '3', '4', '5'])->comment('0 -> organization , 1 -> lab, 2 -> lab_program, 3 -> challenge, 4 -> challenge_path , 5 -> project');
            $table->text('subject');
            $table->text('fr_CA_subject');
            $table->text('body_content');
            $table->text('fr_CA_body_content');
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
        Schema::dropIfExists('email_templates');
    }
};
