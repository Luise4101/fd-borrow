<?php

namespace App\Models\Inventory;

use App\Models\User;
use App\Models\Asset\Category;
use App\Models\Asset\Supplier;
use App\Models\Inventory\AdjustItem;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AdjustHead extends Model
{
    use HasFactory;

    protected $table = 'adjust_heads';
    protected $fillable = ['category_id', 'created_by', 'purchase_at', 'supplier_id', 'updated_by', 'note'];
    protected static function booted() {
        static::deleting(function($adjustHead) {
            foreach($adjustHead->adjustitems as $adjustItem) {
                $adjustItem->reverseStoreQuantity();
            }
        });
    }

    public function category() {
        return $this->belongsTo(Category::class)->where('table_list_id', '12');
    }
    public function supplier() {
        return $this->belongsTo(Supplier::class);
    }
    public function creater() {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }
    public function updater() {
        return $this->belongsTo(User::class, 'updated_by', 'id');
    }
    public function adjustitems(): HasMany {
        return $this->hasMany(AdjustItem::class);
    }
}
