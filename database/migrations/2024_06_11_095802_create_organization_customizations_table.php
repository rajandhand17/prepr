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
        Schema::create('organization_customizations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('organization_id');
            $table->enum('enable_custom_login_and_registration', ['0', '1'])->default('0')->comment('0 -> No,1 -> Yes');
            $table->enum('use_main_org_logo', ['0', '1'])->default('0')->comment('0 -> No,1 -> Yes');
            $table->text('custom_login_url');
            $table->text('custom_logo_image')->nullable();
            $table->text('custom_hero_image')->nullable();
            $table->string('custom_background_color')->nullable();
            $table->foreign('organization_id')->references('id')->on('organizations')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('organization_customizations');
    }
};
