<?php
namespace App\Filament\Resources\Inventory\ProductResource\Pages;

use Filament\Resources\Pages\ViewRecord;
use Illuminate\Contracts\Support\Htmlable;
use App\Filament\Resources\Inventory\ProductResource;

class ViewProduct extends ViewRecord
{
    protected static string $resource = ProductResource::class;

    public function getTitle(): string | Htmlable {
        $record = $this->getRecord();
        return $record->name;
    }
}