<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void {
        Schema::create('return_heads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('borrow_head_id')->constrained()->cascadeOnDelete();
            $table->foreignId('status_id')->nullable()->constrained()->nullOnDelete();
            $table->bigInteger('user_id')->nullable();
            $table->string('user_fullname')->nullable();
            $table->string('user_tel', 40)->nullable();
            $table->string('user_lineid', 50)->nullable();
            $table->string('price_fine_all', 9, 2)->nullable();
            $table->bigInteger('received_by')->nullable();
            $table->string('note', 500)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('return_heads');
    }
};
