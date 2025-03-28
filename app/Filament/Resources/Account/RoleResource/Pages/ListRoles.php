<?php

namespace App\Filament\Resources\Account\RoleResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\Account\RoleResource;

class ListRoles extends ListRecords
{
    protected static string $resource = RoleResource::class;
    protected function getHeaderActions(): array {
        return [ CreateAction::make() ];
    }
}
