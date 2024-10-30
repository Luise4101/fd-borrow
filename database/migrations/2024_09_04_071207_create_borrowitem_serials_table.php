<?php

use App\Models\Main\BorrowItem;
use App\Models\Inventory\Serial;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('borrowitem_serials', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(BorrowItem::class)->constrained()->cascadeOnDelete();;
            $table->foreignIdFor(Serial::class)->constrained()->cascadeOnDelete();;
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('borrowitem_serials');
    }
};
