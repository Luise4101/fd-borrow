<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kongs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('samnak_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('section_id')->nullable()->constrained()->nullOnDelete();
            $table->string('qkong', 20);
            $table->string('ckong', 100);
            $table->integer('credit')->default(0);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kongs');
    }
};
