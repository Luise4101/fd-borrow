<?php

namespace App\Filament\Resources\Asset\StatusResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\Asset\StatusResource;

class ListStatuses extends ListRecords
{
    protected static string $resource = StatusResource::class;
    protected function getHeaderActions(): array {
        return [ CreateAction::make() ];
    }
}
