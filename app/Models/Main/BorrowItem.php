<?php

namespace App\Models\Main;

use App\Models\Inventory\Store;
use App\Models\Main\BorrowHead;
use App\Models\Inventory\Serial;
use App\Models\Inventory\Product;
use Illuminate\Database\Eloquent\Model;
use Spatie\EloquentSortable\SortableTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class BorrowItem extends Model
{
    use HasFactory, SortableTrait;

    protected $table = 'borrow_items';
    protected $fillable = ['borrow_head_id', 'sort', 'product_id', 'q_request', 'q_lend', 'q_all_return'];
    protected $originalQuantityRequest;
    protected $originalQuantityLend;
    protected static function booted() {
        static::creating(function($borrowItem) {
            $maxSort = BorrowItem::where('borrow_head_id', $borrowItem->borrow_head_id)->max('sort');
            $borrowItem->sort = $maxSort ? $maxSort + 1 : 1;
        });
        static::retrieved(function($borrowItem) {
            $borrowItem->originalQuantityRequest = $borrowItem->q_request;
            $borrowItem->originalQuantityLend = $borrowItem->q_lend;
        });
        static::saved(function($borrowItem) {
            $borrowItem->borrowStoreQuantity();
        });
        static::deleted(function($borrowItem) {
            $borrowItem->reverseStoreQuantity();
        });
    }

    public $sortable = ['order_column_name' => 'sort', 'sort_when_creating' => true];
    public function borrowhead() {
        return $this->belongsTo(BorrowHead::class, 'borrow_head_id', 'id');
    }
    public function product() {
        return $this->belongsTo(Product::class);
    }
    public function serials(): BelongsToMany {
        return $this->belongsToMany(Serial::class, 'borrowitem_serials')->withTimestamps();
    }
    public function borrowStoreQuantity() {
        if(!$this->product_id) {return;}
        $store = Store::firstOrCreate(['product_id' => $this->product_id]);
        $originalQuantityRequest = $this->originalQuantityRequest ?? 0;
        $currentQuantityRequest = $this->q_request ?? 0;
        $originalQuantityLend = $this->originalQuantityLend ?? 0;
        $currentQuantityLend = $this->q_lend ?? 0;
        $lendCondition = match(true) {
            $originalQuantityLend == 0 && $currentQuantityLend == 0 => 'lendNone',
            $originalQuantityLend == 0 && $currentQuantityLend != 0 => 'lendNow',
            $originalQuantityLend != 0 && $currentQuantityLend != 0 => 'lendUpdated',
            $originalQuantityLend != 0 && $currentQuantityLend == 0 => 'lendReturned',
        };
        switch($lendCondition) {
            case 'lendNone':
                if ($originalQuantityRequest != 0) {
                    $store->q_book -= $originalQuantityRequest;
                }
                $store->q_book += $currentQuantityRequest;
                break;
            case 'lendNow':
                $store->q_book -= $originalQuantityRequest;
                $store->q_borrow += $currentQuantityLend;
                break;
            case 'lendUpdated':
                $store->q_borrow -= $originalQuantityLend;
                $store->q_borrow += $currentQuantityLend;
                break;
            case 'lendReturned':
                $store->q_book += $currentQuantityRequest;
                $store->q_borrow -= $originalQuantityLend;
                break;
        }
        $store->save();
        $this->originalQuantityRequest = $this->q_request;
        $this->originalQuantityLend = $this->q_lend;
    }
    public function reverseStoreQuantity() {
        if(!$this->product_id) {return;}
        $store = Store::where('product_id', $this->product_id)->first();
        if($store) {
            $currentQuantityRequest = $this->q_request ?? 0;
            $currentQuantityLend = $this->q_lend ?? 0;
            if ($currentQuantityLend == 0) {
                $store->q_book -= $currentQuantityRequest;
            } else {
                $store->q_borrow -= $currentQuantityLend;
            }
            $store->save();
        }
    }
}
