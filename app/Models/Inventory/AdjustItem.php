<?php

namespace App\Models\Inventory;

use App\Models\Inventory\Store;
use App\Models\Inventory\Serial;
use App\Models\Inventory\Product;
use App\Models\Inventory\AdjustHead;
use Illuminate\Database\Eloquent\Model;
use Spatie\EloquentSortable\SortableTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class AdjustItem extends Model
{
    use HasFactory, SortableTrait;

    protected $table = 'adjust_items';
    protected $fillable = ['adjust_head_id', 'sort', 'product_id', 'effective', 'quantity'];
    protected $originalQuantity;
    protected $originalEffective;
    protected static function booted() {
        static::creating(function($adjustItem) {
            $maxSort = AdjustItem::where('adjust_head_id', $adjustItem->adjust_head_id)->max('sort');
            $adjustItem->sort = $maxSort ? $maxSort + 1 : 1;
        });
        static::retrieved(function($adjustItem) {
            $adjustItem->originalQuantity = $adjustItem->quantity;
            $adjustItem->originalEffective = $adjustItem->effective;
        });
        static::saved(function($adjustItem) {
            $adjustItem->adjustStoreQuantity();
        });
        static::deleted(function($adjustItem) {
            $adjustItem->reverseStoreQuantity();
        });
    }

    public $sortable = [ 'order_column_name' => 'sort', 'sort_when_creating' => true ];
    public function adjusthead() {
        return $this->belongsTo(AdjustHead::class, 'adjust_head_id','id');
    }
    public function product() {
        return $this->belongsTo(Product::class);
    }
    public function serials(): BelongsToMany {
        return $this->belongsToMany(Serial::class, 'adjustitem_serials')->withTimestamps();
    }
    public function adjustStoreQuantity() {
        if(!$this->product_id || !$this->quantity) {return;}
        $store = Store::firstOrCreate(['product_id' => $this->product_id]);
        if($this->originalEffective == 'plus') {
            $store->q_all -= $this->originalQuantity;
        } elseif ($this->originalEffective == 'minus') {
            $store->q_all += $this->originalQuantity;
        }
        if($this->effective == 'plus') {
            $store->q_all += $this->quantity;
        } elseif ($this->effective == 'minus') {
            $store->q_all -= $this->quantity;
        }
        $store->save();
    }
    public function reverseStoreQuantity() {
        if(!$this->product_id || !$this->quantity) {return;}
        $store = Store::where('product_id', $this->product_id)->first();
        if($store) {
            if($this->effective == 'plus') {
                $store->q_all -= $this->quantity;
            } elseif ($this->effective == 'minus') {
                $store->q_all += $this->quantity;
            }
            $store->save();
        }
    }
}
