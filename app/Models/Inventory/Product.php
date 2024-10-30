<?php

namespace App\Models\Inventory;

use App\Models\User;
use App\Models\Asset\Brand;
use App\Models\Asset\Category;
use App\Models\Inventory\Store;
use App\Models\Inventory\Serial;
use App\Models\Inventory\AdjustItem;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;

    protected $fillable = ['category_id','brand_id','name','img','price_product','price_borrow','created_by','updated_by'];
    protected $casts = ['img' => 'array'];

    public function category() {
        return $this->belongsTo(Category::class)->where('table_list_id', '9');
    }
    public function brand() {
        return $this->belongsTo(Brand::class);
    }
    public function creater() {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }
    public function updater() {
        return $this->belongsTo(User::class, 'updated_by', 'id');
    }
    public function serials(): HasMany {
        return $this->hasmany(Serial::class);
    }
    public function store(): HasOne {
        return $this->hasOne(Store::class);
    }
    public function adjustItems(): HasMany {
        return $this->hasMany(AdjustItem::class);
    }
}
