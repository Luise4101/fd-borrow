<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('brand_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name', 100);
            $table->string('img', 1000)->nullable();
            $table->decimal('price_product', 9, 2);
            $table->decimal('price_borrow', 9, 2)->nullable();
            $table->datetime('created_at')->nullable();
            $table->bigInteger('created_by')->nullable();
            $table->datetime('updated_at')->nullable();
            $table->bigInteger('updated_by')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
