<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('repair_heads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('status_id')->nullable()->constrained()->nullOnDelete();
            $table->datetime('created_at')->nullable();
            $table->bigInteger('created_by')->nullable();
            $table->datetime('sended_at')->nullable();
            $table->datetime('returned_at')->nullable();
            $table->decimal('price_repair_all',9,2)->default(0);
            $table->foreignId('supplier_id')->nullable()->constrained()->nullOnDelete();
            $table->string('attachment',1000)->nullable();
            $table->datetime('updated_at')->nullable();
            $table->bigInteger('updated_by')->nullable();
            $table->string('note',500)->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('repair_heads');
    }
};
