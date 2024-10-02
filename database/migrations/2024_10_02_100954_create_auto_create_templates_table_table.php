<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('auto_create_templates_table', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('language', 191)->default('en');
            $table->string('role_type', 191)->nullable();
            $table->string('role_user_type', 191)->nullable();
            $table->text('lab_id')->nullable();
            $table->text('challenge_id')->nullable();
            $table->text('project_id')->nullable();
            $table->text('lab_group_id')->nullable();
            $table->text('challenge_group_id')->nullable();
            $table->enum('invite_labs', ['0', '1'])->default('0');
            $table->enum('invite_challenges', ['0', '1'])->default('0');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('auto_create_templates_table');

    }
};
