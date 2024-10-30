<?php

namespace App\Filament\Resources\Asset\SupplierResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\Asset\SupplierResource;

class ListSuppliers extends ListRecords
{
    protected static string $resource = SupplierResource::class;
    protected function getHeaderActions(): array {
        return [ CreateAction::make() ];
    }
}
