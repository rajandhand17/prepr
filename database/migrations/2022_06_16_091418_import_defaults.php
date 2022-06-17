<?php

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		// Get Data from ENV
		$name = env("DEV_NAME", FALSE);
		$email = env("DEV_EMAIL", FALSE);
		$password = env("DEV_PASSWORD", FALSE);

		// Check all the fields before registering admin
        if($name && $email && $password){
            // Create Super Admin By Default
            DB::table('users')->insert([
				'name'		=> $name,
                'email'     => $email,
                'password'  => Hash::make($password),
                'email_verified_at' => Carbon::now()
            ]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
};
