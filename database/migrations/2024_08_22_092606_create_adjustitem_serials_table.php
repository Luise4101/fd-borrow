<?php

use App\Models\Inventory\Serial;
use App\Models\Inventory\AdjustItem;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('adjustitem_serials', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(AdjustItem::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Serial::class)->constrained()->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('adjustitem_serials');
    }
};
