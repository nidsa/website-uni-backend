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
        Schema::create('tbdepartment', function (Blueprint $table) {
            $table->id('dep_id');
            $table->foreignId('dep_sec')->nullable()->constrained('tbsection', 'sec_id')->onDelete('set null');
            $table->string('dep_title', 100)->nullable();
            $table->text('dep_detail')->nullable();
            $table->foreignId('dep_img1')->nullable()->constrained('tbimage', 'image_id')->onDelete('set null');
            $table->foreignId('dep_img2')->nullable()->constrained('tbimage', 'image_id')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbdepartment');
    }
};
