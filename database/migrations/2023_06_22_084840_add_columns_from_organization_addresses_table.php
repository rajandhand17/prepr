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
        Schema::table('organization_addresses', function (Blueprint $table) {
            $table->renameColumn('address', 'full_address');
            $table->text('address_1')->nullable()->after('address');
            $table->text('address_2')->nullable()->after('address_1');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('organization_addresses', function (Blueprint $table) {
            //
        });
    }
};
