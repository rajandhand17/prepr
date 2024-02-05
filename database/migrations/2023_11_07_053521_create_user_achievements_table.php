<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('user_achievements', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('certificate_number');
            $table->string('title');
            $table->longText('description')->nullable();
            $table->enum('achievement_type', ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9', '10'])->comment('0->lab,1->labprogram,2->challenge,3->challengepath,4->resourcegroup,5->appreciationaward,6->activityaward,7->skillactivity,8->importedaward,9->winneraward,10->participationaward')->default('0');
            $table->unsignedBigInteger('module_id')->nullable();
            $table->string('module_title')->nullable();
            $table->unsignedBigInteger('module_parent_id')->nullable();
            $table->string('module_parent_title')->nullable();
            $table->string('achievement_prize')->nullable();
            $table->integer('achievement_points')->nullable();
            $table->string('achievement_image')->nullable();
            $table->dateTime('issue_date')->nullable();
            $table->dateTime('valid_date')->nullable();
            $table->string('user_notified')->nullable();
            $table->enum('is_featured', ['0', '1'])->comment('0->no, 1->yes')->default('0');
            $table->string('promo_code')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_achievements');
    }
};
