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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('device_token')->nullable();
            $table->string('name');
            $table->string('first_name')->default(null);
            $table->string('last_name')->default(null);
            $table->string('username')->default(null);
            $table->string('email')->unique()->default(null);
            $table->string('password')->default(null);
            $table->string('country_code')->default(null);
            $table->enum('verification',['0','1'])->default('0')->comment('1=>verified,0=>Not verify');
            $table->enum('two_factor',['allow','disallow'])->default('disallow');
            $table->string('two_factor_otp')->nullable();
            $table->enum('is_login',['0','1'])->default('0');
            $table->string('profile_image')->nullable();
            $table->string('phone_number')->default("front/img/default-user.png")->nullable();
            $table->enum('fr_request',['allow','disallow'])->default('allow');
            $table->enum('fr_accept',['allow','disallow'])->default('disallow');
            $table->integer("point")->default('251');
            $table->integer('rank')->default('0');
            $table->rememberToken();
            $table->enum('is_verify',['0','1'])->default('0')->comment('1=>verified,0=>Not verify');
            $table->enum('is_email_sent',['0','1'])->default('0')->comment('1=>verified,0=>Not verify');
            $table->string('verify_token')->default(null);
            $table->string('mycode')->default(null);
            $table->string('isReferralOpen')->default(null);
            $table->enum('manage_alerts',['0','1'])->default('0')->comment('1=>yes,0=>No');
            $table->enum('is_subscribe',['0','1'])->default('0')->comment('1=>yes,0=>No');
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
        Schema::dropIfExists('users');
    }
};
