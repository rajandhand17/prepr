<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddColumnsToFeaturedModuleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('featured_module', function (Blueprint $table) {
            // Add new columns in the featured_module table
            $table->string('title')->after('id');
            $table->string('description')->nullable()->after('title');
            $table->string('button_text')->default('view')->nullable()->after('module_id');
            $table->string('role')->nullable()->after('button_text');
            $table->string('media_type')->default('image')->nullable()->after('role');
            $table->text('media')->nullable()->after('media_type');
        });

        // Modify the 'module_type' column
        // Using raw SQL to drop the column if it exists and add it again with new options
        DB::statement('SET FOREIGN_KEY_CHECKS=0;'); // Temporarily disable foreign key checks

        // Drop the column if it exists
        if (Schema::hasColumn('featured_module', 'module_type')) {
            Schema::table('featured_module', function (Blueprint $table) {
                $table->dropColumn('module_type');
            });
        }

        // Add the column with new enum values
        Schema::table('featured_module', function (Blueprint $table) {
            $table->enum('module_type', [
                '0', '1', '2', '3', '4', '5', '6', '7',
            ])->comment('0->labs,1->lab_programs,2->challenges,3->challenge_paths,4->resource_modules,5->resource_collections,6->resource_group,7->projects')->after('module_id');
        });

        DB::statement('SET FOREIGN_KEY_CHECKS=1;'); // Re-enable foreign key checks
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('featured_module', function (Blueprint $table) {
            // Drop the new columns
            $table->dropColumn(['title', 'description', 'button_text', 'role', 'media_type', 'media']);

            // Drop the column if it exists
            if (Schema::hasColumn('featured_module', 'module_type')) {
                $table->dropColumn('module_type');
            }

            // Recreate the 'module_type' column with original enum values
            $table->enum('module_type', [
                '0', '1', '2', '3', '4',
            ])->comment('0->labs,1->challenge,2->resource group,3->resource module,4->resource collection')->after('media_type');
        });
    }
}
