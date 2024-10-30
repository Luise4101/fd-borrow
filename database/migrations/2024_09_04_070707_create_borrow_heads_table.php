<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('borrow_heads', function (Blueprint $table) {
            $table->id();
            $table->datetime('created_at')->nullable();
            $table->bigInteger('borrower_id')->nullable();
            $table->string('borrower_tel', 40)->nullable();
            $table->string('borrower_lineid', 50)->nullable();
            $table->foreignId('status_id')->nullable()->constrained()->nullOnDelete();
            $table->string('qsamnak', 20)->nullable();
            $table->string('qsection', 20)->nullable();
            $table->string('qkong', 20)->nullable();
            $table->string('qhead', 50)->nullable();
            $table->string('chead', 100)->nullable();
            $table->datetime('approved_at')->nullable();
            $table->datetime('pickup_at')->nullable();
            $table->datetime('return_schedule')->nullable();
            $table->datetime('return_at')->nullable();
            $table->string('activity_name', 200);
            $table->string('activity_place', 200);
            $table->integer('q_attendee')->default(0)->nullable();
            $table->integer('q_staff')->default(0)->nullable();
            $table->string('attachment', 1000)->nullable();
            $table->decimal('price_borrow_all', 9, 2)->nullable();
            $table->decimal('price_fine', 9, 2)->nullable();
            $table->string('proof_payment', 1000)->nullable();
            $table->datetime('updated_at')->nullable();
            $table->bigInteger('updated_by')->nullable();
            $table->string('note', 500)->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('borrow_heads');
    }
};
