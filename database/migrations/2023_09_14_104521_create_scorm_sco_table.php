<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('scorm_sco', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('scorm_id');
            $table->text('uuid');
            $table->bigInteger('sco_parent_id')->nullable();
            $table->string('entry_url')->nullable();
            $table->text('identifier');
            $table->text('title');
            $table->tinyInteger('visible');
            $table->longText('sco_parameters')->nullable();
            $table->longText('launch_data')->nullable();
            $table->string('max_time_allowed')->nullable();
            $table->string('time_limit_action')->nullable();
            $table->tinyInteger('block');
            $table->integer('score_int')->nullable();
            $table->decimal('score_decimal')->nullable();
            $table->decimal('completion_threshold')->nullable();
            $table->string('prerequisites')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scorm_sco');
    }
};
