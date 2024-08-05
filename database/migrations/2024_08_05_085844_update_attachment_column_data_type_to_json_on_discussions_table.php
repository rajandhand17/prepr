<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('discussions')->get()->each(function ($discussion) {
            if (!is_null($discussion->attachment)) {
                $validJson = json_encode([$discussion->attachment]);
                DB::table('discussions')
                    ->where('id', $discussion->id)
                    ->update(['attachment' => $validJson]);
            }
        });

        Schema::table('discussions', function (Blueprint $table) {
            $table->json('attachment')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('discussions', function (Blueprint $table) {
            $table->string('attachment', 191)->nullable()->change();
        });
    }
};
