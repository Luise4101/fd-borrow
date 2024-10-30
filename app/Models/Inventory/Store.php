<?php

namespace App\Models\Inventory;

use App\Models\Inventory\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Store extends Model
{
    use HasFactory;
    protected $fillable = ['product_id','q_all','q_waste','q_borrow','q_book'];

    public function product() {
        return $this->belongsTo(Product::class);
    }
}
