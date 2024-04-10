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
        Schema::create('user_settings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->enum('profile_privacy', ['0', '1', '2'])->comment('0 -> public(anyone),1->private(no-one),2->signed-user')->default('0');
            $table->enum('friend_request_privacy', ['0', '1'])->comment('0->any-one,1->no-one')->default('0');
            $table->enum('project_privacy', ['0', '1'])->comment('0 -> public, 1 -> private')->default('0');
            $table->enum('manage_alerts', ['0', '1'])->comment('0 -> no, 1 -> yes')->default('0');
            $table->enum('is_subscribe', ['0', '1'])->comment('0 -> unsubscribed, 1 -> subscribed')->default('0');
            $table->enum('newsfeeds', ['0', '1', '2', '3'])->comment('0 -> friends&followers, 1 -> friends, 2 -> followers, 3 -> none')->default('0');
            $table->enum('email_subscription_notification', ['0', '1'])->comment('0 -> unsubscribed, 1 -> subscribed')->default('0');
            $table->enum('email_subscription_network_summary', ['0', '1'])->comment('0 -> unsubscribed, 1 -> subscribed')->default('0');
            $table->enum('email_subscription_challenge_summary', ['0', '1', '2'])->comment('0 -> unsubscribed, 1 -> monthly, 2 -> weekly')->default('0');
            $table->enum('email_subscription_lab_summary', ['0', '1', '2'])->comment('0 -> unsubscribed, 1 -> monthly, 2 -> weekly')->default('0');
            $table->enum('display_lab_minionboarding', ['0', '1'])->comment('0 -> unsubscribed, 1 -> monthly, 2 -> weekly')->default('0');
            $table->enum('display_challenge_minionboarding', ['0', '1'])->comment('0 -> unsubscribed, 1 -> monthly, 2 -> weekly')->default('0');
            $table->enum('display_org_minionboarding', ['0', '1'])->comment('0 -> unsubscribed, 1 -> monthly, 2 -> weekly')->default('0');
            $table->enum('fcm_notification_permission', ['0', '1'])->comment('0 -> no, 1 -> yes')->default('0');
            $table->string('fcm_device_token')->nullable();
            $table->enum('challenge_recommends', ['0', '1', '2'])->comment('0 -> unsubscribed, 1 -> monthly, 2 -> weekly')->default('0');
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
        Schema::dropIfExists('user_settings');
    }
};
