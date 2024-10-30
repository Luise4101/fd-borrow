<?php

namespace App\Filament\Resources\Asset\BrandResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\Asset\BrandResource;

class ListBrands extends ListRecords
{
    protected static string $resource = BrandResource::class;
    protected function getHeaderActions(): array {
        return [ CreateAction::make() ];
    }
}
