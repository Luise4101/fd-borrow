<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('borrow_infos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('borrow_head_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('sort')->default(0);
            $table->string('purpose', 200);
            $table->integer('q_use')->default(0);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('borrow_infos');
    }
};
