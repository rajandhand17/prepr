<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->text('device_token')->nullable();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('name')->nullable();
            $table->string('username')->nullable();
            $table->string('email')->nullable()->index();
            $table->enum('verification', ['0', '1'])->comment("0 not verify,1 for verified")->default('0');
            $table->string('country_code')->nullable();
            $table->string('phone_number')->nullable();
            $table->enum('two_factor', ['allow', 'disallow'])->default('disallow');
            $table->string('two_factor_otp')->nullable();
            $table->enum('is_login', ['0', '1'])->default('0');
            $table->string('password')->nullable();
            $table->string('profile_image')->nullable();
            $table->enum('fr_accept', ['allow', 'disallow'])->default('disallow');
            $table->enum('fr_request', ['allow', 'disallow'])->default('allow');
            $table->enum('follow', ['allow', 'disallow'])->default('allow');
            $table->enum('ch_accept', ['allow', 'disallow'])->default('allow');
            $table->integer('point')->nullable();
            $table->integer('rank')->nullable(); 
            $table->rememberToken();
            $table->enum('is_verify', ['0', '1'])->default('0');
            $table->enum('is_email_sent', ['0', '1'])->default('0');
            $table->string('verify_token')->nullable();
            $table->string('mycode')->nullable();
            $table->text('linkedin')->nullable();
            $table->string('isReferralOpen')->nullable();
            $table->enum('manage_alerts', ['0', '1'])->default('0');
            $table->enum('is_subscribe', ['subscribe', 'unsubscribe'])->default('subscribe');
            $table->enum('is_profile_completed', ['0', '1'])->comment('0 for not completed and 1 for completed')->default('0');
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
}
