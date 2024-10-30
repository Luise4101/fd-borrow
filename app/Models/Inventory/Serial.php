<?php

namespace App\Models\Inventory;

use App\Models\Asset\Status;
use App\Models\Main\BorrowItem;
use App\Models\Inventory\Product;
use App\Models\Inventory\AdjustItem;
use App\Models\Inventory\RepairItem;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Serial extends Model
{
    use HasFactory;

    protected $fillable = ['product_id', 'name', 'serial_number', 'license', 'status_id'];

    public function product() {
        return $this->belongsTo(Product::class)->where('category_id', '3');
    }
    public function status() {
        return $this->belongsTo(Status::class)->where('table_list_id', '10');
    }
    public function adjustitems(): BelongsToMany {
        return $this->belongsToMany(AdjustItem::class, 'adjustitem_serials');
    }
    public function repairitems(): BelongsToMany {
        return $this->belongsToMany(RepairItem::class, 'repairitem_serials');
    }
    public function borrowitems(): BelongsToMany {
        return $this->belongsToMany(BorrowItem::class, 'borrowitem_serials');
    }
}
