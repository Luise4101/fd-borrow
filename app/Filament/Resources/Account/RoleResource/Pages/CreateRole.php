<?php

namespace App\Filament\Resources\Account\RoleResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\Account\RoleResource;

class CreateRole extends CreateRecord
{
    protected static string $resource = RoleResource::class;
    protected function mutateFormDataBeforeCreate(array $data): array {
        foreach($data as $key=>$val){ if(is_string($val)){ $data[$key] = trim($val); } }
        return $data;
    }
}
