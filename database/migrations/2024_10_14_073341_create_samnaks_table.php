<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('samnaks', function (Blueprint $table) {
            $table->id();
            $table->string('qsamnak', 20);
            $table->string('csamnak', 100);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('samnaks');
    }
};
