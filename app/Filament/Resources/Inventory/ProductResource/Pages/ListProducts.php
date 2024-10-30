<?php

namespace App\Filament\Resources\Inventory\ProductResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\Inventory\ProductResource;

class ListProducts extends ListRecords
{
    protected static string $resource = ProductResource::class;
    protected function getHeaderActions(): array {
        return [CreateAction::make()];
    }
}
