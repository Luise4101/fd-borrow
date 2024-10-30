<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('adjust_heads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->datetime('created_at')->nullable();
            $table->bigInteger('created_by')->nullable();
            $table->datetime('purchase_at')->nullable();
            $table->foreignId('supplier_id')->nullable()->constrained()->nullOnDelete();
            $table->datetime('updated_at')->nullable();
            $table->bigInteger('updated_by')->nullable();
            $table->string('note', 500)->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('adjust_heads');
    }
};
