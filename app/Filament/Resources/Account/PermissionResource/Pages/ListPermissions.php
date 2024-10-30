<?php

namespace App\Filament\Resources\Account\PermissionResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\Account\PermissionResource;

class ListPermissions extends ListRecords
{
    protected static string $resource = PermissionResource::class;
    protected function getHeaderActions(): array {
        return [ CreateAction::make() ];
    }
}
