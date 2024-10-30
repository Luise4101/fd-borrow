<?php

use App\Models\Inventory\Serial;
use App\Models\Inventory\RepairItem;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('repairitem_serials', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(RepairItem::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Serial::class)->constrained()->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('repairitem_serials');
    }
};
