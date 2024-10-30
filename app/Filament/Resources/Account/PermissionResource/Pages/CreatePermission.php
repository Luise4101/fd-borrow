<?php

namespace App\Filament\Resources\Account\PermissionResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\Account\PermissionResource;

class CreatePermission extends CreateRecord
{
    protected static string $resource = PermissionResource::class;
    protected function mutateFormDataBeforeCreate(array $data): array {
        foreach ($data as $key=>$val) {if (is_string($val)) { $data[$key] = trim($val); }}
        return $data;
    }
}
