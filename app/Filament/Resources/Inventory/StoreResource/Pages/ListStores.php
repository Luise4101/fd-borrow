<?php

namespace App\Filament\Resources\Inventory\StoreResource\Pages;

use App\Models\Inventory\Store;
use App\Models\Inventory\Product;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\Inventory\StoreResource;

class ListStores extends ListRecords
{
    protected static string $resource = StoreResource::class;

    public function __construct() {
        $product = Product::get();
        foreach($product as $pro) {
            $found = Store::where('product_id',$pro->id)->exists();
            if(!$found) {
                Store::create([
                    'product_id' => $pro->id,
                    'q_all' => 0,
                    'q_waste' => 0,
                    'q_borrow' => 0,
                    'q_book' => 0
                ]);
            }
        }
    }
}
