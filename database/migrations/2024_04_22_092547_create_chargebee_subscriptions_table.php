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
        Schema::create('chargebee_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('organization_id');
            $table->string('plan')->comment('name of plan');
            $table->enum('plan_validity', ['0', '1'])->default('0')->comment('0 -> monthly, 1 -> yearly');
            $table->enum('plan_limitations', ['0', '1'])->default('0')->comment('0 -> limited, 1 -> unlimited');
            $table->string('trial_end_date')->nullable()->comment('Plan next billing date');
            $table->integer('challenge_limits')->nullable()->comment('Challenge Creation Limit, if null then it has no limits in challenge');
            $table->integer('challenge_path_limits')->nullable()->comment('Challenge Path Creation Limit, if null then it has no limits in challenge path');
            $table->integer('lab_limits')->nullable()->comment('Lab Creation Limit, if null then it has no limits in lab');
            $table->integer('lab_program_limits')->nullable()->comment('Lab Program Creation Limit, if null then it has no limits in lab program');
            $table->integer('pre_build_lab_limits')->nullable()->comment('Pre build lab, if null then it has no limits in pre build lab ');
            $table->integer('resource_module_limits')->nullable()->comment('Resource Module Creation Limit, if null then it has no limits in resource module');
            $table->integer('resource_collection_limits')->nullable()->comment('Resource Collection Creation Limit, if null then it has no limits in resource collection');
            $table->integer('resource_group_limits')->nullable()->comment('Resource Group Creation Limit, if null then it has no limits in resource group');
            $table->integer('user_invite_limits')->nullable()->comment('The number of participants you can invite to your Organisation.');
            $table->integer('organization_invite_limits')->nullable()->comment('The number of managers you can invite under your Organisation.');
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
        Schema::dropIfExists('chargebee_subscriptions');
    }
};
