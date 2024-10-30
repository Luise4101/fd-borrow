<?php

namespace App\Models\Inventory;

use App\Models\Asset\Status;
use App\Models\Inventory\Serial;
use App\Models\Inventory\Product;
use App\Models\Inventory\RepairHead;
use Illuminate\Database\Eloquent\Model;
use Spatie\EloquentSortable\SortableTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class RepairItem extends Model
{
    use HasFactory, SortableTrait;

    protected $table = 'repair_items';
    protected $fillable = ['repair_head_id', 'sort', 'product_id', 'quantity', 'price_repair', 'status_id', 'note'];
    protected $originalQuantity;
    protected $originalStatusId;
    protected static function booted() {
        static::creating(function($repairItem) {
            $maxSort = RepairItem::where('repair_head_id', $repairItem->repair_head_id)->max('sort');
            $repairItem->sort = $maxSort ? $maxSort + 1 : 1;
        });
        static::retrieved(function($repairItem) {
            $repairItem->originalQuantity = $repairItem->quantity;
            $repairItem->originalStatusId = $repairItem->status_id;
        });
        static::saved(function($repairItem) {
            $repairItem->repairStoreQuantity();
        });
        static::deleted(function($repairItem) {
            $repairItem->reverseStoreQuantity();
        });
    }

    public $sortable = ['order_column_name' => 'sort', 'sort_when_creating' => true];
    public function repairhead() {
        return $this->belongsTo(RepairHead::class, 'repair_head_id', 'id');
    }
    public function product() {
        return $this->belongsTo(Product::class);
    }
    public function status() {
        return $this->belongsTo(Status::class)->where('table_list_id', '14');
    }
    public function serials(): BelongsToMany {
        return $this->belongsToMany(Serial::class, 'repairitem_serials')->withTimestamps();
    }
    public function repairStoreQuantity() {
        if(!$this->product_id || !$this->quantity) {
            return;
        }
        $store = Store::firstOrCreate(['product_id' => $this->product_id]);
        $originalQuantity = $this->originalQuantity ?? 0;
        $currentQuantity = $this->quantity;
        $originalStatusId = $this->originalStatusId;
        $currentStatusId = $this->status_id;
        $isOriginalSpecial = ($originalStatusId == 17 || $originalStatusId == 18);
        $isCurrentSpecial = ($currentStatusId == 17 || $currentStatusId == 18);
        switch(true) {
            case !$this->exists && !$isCurrentSpecial:
                $store->q_waste += $currentQuantity;
                break;
            case !$isOriginalSpecial && !$isCurrentSpecial:
                $store->q_waste -= $originalQuantity;
                $store->q_waste += $currentQuantity;
                break;
            case !$isOriginalSpecial && $isCurrentSpecial:
                $store->q_waste -= $originalQuantity;
                break;
            case $isOriginalSpecial && !$isCurrentSpecial:
                $store->q_waste += $currentQuantity;
                break;
        }
        $store->save();
        $this->originalQuantity = $this->quantity;
        $this->originalStatusId = $this->status_id;
    }
    public function reverseStoreQuantity() {
        if(!$this->product_id || !$this->quantity || $this->status_id == '17' || $this->status_id == '18') {
            return;
        }
        $store = Store::where('product_id', $this->product_id)->first();
        if($store) {
            $store->q_waste -= $this->quantity;
            $store->save();
        }
    }
}
