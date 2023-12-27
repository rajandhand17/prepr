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
        Schema::create('template_lab_programs_skills_groups_stack', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('template_lab_program_id');
            $table->integer('foreign_id');
            $table->enum('type', ['0', '1', '2'])->comment('0->skills, 1->group,2->stack');
            $table->foreign('template_lab_program_id','tlp_skills_groups_stack_template_lab_program_id_foreign')->references('id')->on('template_lab_program')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('template_lab_programs_skills_groups_stack');
    }
};
