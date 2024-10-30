<?php

namespace App\Filament\Resources\Asset\CategoryResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\Asset\CategoryResource;

class ListCategories extends ListRecords
{
    protected static string $resource = CategoryResource::class;
    protected function getHeaderActions(): array {
        return [ CreateAction::make() ];
    }
}
