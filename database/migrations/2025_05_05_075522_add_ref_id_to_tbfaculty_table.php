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
        Schema::table('tbfaculty', function (Blueprint $table) {
            $table->integer('ref_id')->nullable()->after('f_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tbfaculty', function (Blueprint $table) {
            $table->dropColumn('ref_id');
        });
    }
};
