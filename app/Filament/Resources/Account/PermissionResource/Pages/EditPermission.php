<?php

namespace App\Filament\Resources\Account\PermissionResource\Pages;

use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\Account\PermissionResource;

class EditPermission extends EditRecord
{
    protected static string $resource = PermissionResource::class;
    protected function mutateFormDataBeforeSave(array $data): array {
        foreach($data as $key=>$val){ if(is_string($val)){ $data[$key] = trim($val); } }
        return $data;
    }
}
