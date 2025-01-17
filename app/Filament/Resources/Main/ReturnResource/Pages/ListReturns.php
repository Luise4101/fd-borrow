<?php

namespace App\Filament\Resources\Main\ReturnResource\Pages;

use App\Filament\Resources\Main\ReturnResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListReturns extends ListRecords
{
    protected static string $resource = ReturnResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
