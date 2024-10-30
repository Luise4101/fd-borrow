<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('samnak_id')->nullable()->constrained()->nullOnDelete();
            $table->string('qsection', 20);
            $table->string('csection', 100);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sections');
    }
};
