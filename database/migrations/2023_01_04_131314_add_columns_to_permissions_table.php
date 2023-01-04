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
        Schema::table('permissions', function (Blueprint $table) {
            $table->string('guard_name')->nullable()->after('description');
            $table->bigInteger('order_by')->nullable()->after('guard_name');
            $table->enum('category',['Lab Management','Challenge Management','Other Challenge Permissions','Organisation Management','Resources Management','Other Resources Management','Project Management','User Management','Other Organization Management','Permission Management'])->nullable()->after('order_by');
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
        Schema::table('permissions', function (Blueprint $table) {
            $table->dropColumn('guard_name');
            $table->dropColumn('order_by');
            $table->dropColumn('category');

        });
    }
};
