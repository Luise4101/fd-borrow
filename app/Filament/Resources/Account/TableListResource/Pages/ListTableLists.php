<?php

namespace App\Filament\Resources\Account\TableListResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\Account\TableListResource;

class ListTableLists extends ListRecords
{
    protected static string $resource = TableListResource::class;
    protected function getHeaderActions(): array {
        return [ CreateAction::make() ];
    }
}
