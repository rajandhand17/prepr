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
        Schema::create('unified_connections', function (Blueprint $table) {
            $table->id();
            $table->morphs('model');
            $table->string('connection_id');
            $table->string('connection_type');
            $table->enum('usage_type', ['0', '1', '2'])->comment('0 -> organization_member_invite, 1 -> lab_member_invite, 2-> challenge_member_invite');
            $table->foreignId('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('unified_connections');
    }
};
