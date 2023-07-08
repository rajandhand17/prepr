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
        Schema::create('groups', function (Blueprint $table) {
            $table->id();
            $table->string('language')->default('en');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('organization_id');
            $table->unsignedBigInteger('category_id');
            $table->unsignedBigInteger('challenge_id');
            $table->unsignedBigInteger('lab_id');
            $table->unsignedBigInteger('resource_id');
            $table->unsignedBigInteger('collection_id');
            $table->string('slug')->nullable();
            $table->string('title')->nullable();
            $table->longText('description')->nullable();
            $table->enum('type',['0','1','2'])->default('0')->comment('lab', 'challenge', 'resource');
            $table->text('media')->nullable();
            $table->enum('status', ['0', '1'])->comment('0->open,1->closed')->default('0');
            $table->enum('privacy', ['0', '1'])->comment('0->no,1->yes')->default('0');
            $table->enum('challenge_privacy', ['0', '1'])->comment('0->no,1->yes')->default('0');
            $table->enum('project_privacy', ['0', '1'])->comment('0->no,1->yes')->default('0');
            $table->enum('published', ['0', '1'])->comment('0->draft,1->published')->default('1');
            $table->text('prize')->nullable();
            $table->integer('points')->nullable();
            $table->string('trophy')->nullable();
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
        Schema::dropIfExists('groups');
    }
};
