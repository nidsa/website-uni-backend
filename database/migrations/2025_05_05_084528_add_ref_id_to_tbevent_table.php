<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tbevent', function (Blueprint $table) {
            $table->integer('ref_id')->nullable()->after('e_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tbevent', function (Blueprint $table) {
            $table->dropColumn('ref_id');
        });
    }
};
