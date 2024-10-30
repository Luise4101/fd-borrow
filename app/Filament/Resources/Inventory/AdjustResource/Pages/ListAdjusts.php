<?php

namespace App\Filament\Resources\Inventory\AdjustResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\Inventory\AdjustResource;

class ListAdjusts extends ListRecords
{
    protected static string $resource = AdjustResource::class;
    protected function getHeaderActions(): array {
        return [CreateAction::make()];
    }
}
