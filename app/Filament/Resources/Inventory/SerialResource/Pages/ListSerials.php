<?php

namespace App\Filament\Resources\Inventory\SerialResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\Inventory\SerialResource;

class ListSerials extends ListRecords
{
    protected static string $resource = SerialResource::class;
    protected function getHeaderActions(): array {
        return [CreateAction::make()];
    }
}
