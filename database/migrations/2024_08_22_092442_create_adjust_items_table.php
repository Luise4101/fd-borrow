<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('adjust_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('adjust_head_id')->constrained('adjust_heads', 'id')->cascadeOnDelete();
            $table->unsignedInteger('sort')->default(0);
            $table->foreignId('product_id')->nullable();
            $table->string('effective', 10);
            $table->decimal('quantity', 9,2)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('adjust_items');
    }
};
