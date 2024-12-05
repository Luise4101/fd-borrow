<?php

use App\Models\Main\ReturnItem;
use App\Models\Inventory\Serial;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void {
        Schema::create('returnitem_serials', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(ReturnItem::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Serial::class)->constrained()->cascadeOnDelete();
            $table->foreignId('status_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('returnitem_serials');
    }
};
