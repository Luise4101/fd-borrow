<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void {
        Schema::create('return_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('return_head_id')->constrained()->cascadeOnDelete();
            $table->foreignId('borrow_item_id')->nullable();
            $table->unsignedInteger('sort')->default(0);
            $table->integer('q_return')->default(0);
            $table->integer('q_broken')->default(0);
            $table->decimal('fine_broken', 9, 2)->default(0);
            $table->integer('q_missing')->default(0);
            $table->decimal('fine_missing', 9, 2)->default(0);
            $table->string('note')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('return_items');
    }
};
