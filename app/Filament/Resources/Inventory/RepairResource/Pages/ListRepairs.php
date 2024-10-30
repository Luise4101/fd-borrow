<?php

namespace App\Filament\Resources\Inventory\RepairResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\Inventory\RepairResource;

class ListRepairs extends ListRecords
{
    protected static string $resource = RepairResource::class;
    protected function getHeaderActions(): array {
        return [CreateAction::make()];
    }
}
