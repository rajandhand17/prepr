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
        Schema::create('organization_addresses', function (Blueprint $table) {
            $table->id();
            $table->integer('organization_id');
            $table->string('latitude');
            $table->string('longitude');
            $table->text('address');
            $table->text('city');
            $table->text('state');
            $table->text('country');
            $table->text('zip_code');
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('organization_id')->references('id')->on('organizations')
                ->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('organization_addresses');
    }
};
