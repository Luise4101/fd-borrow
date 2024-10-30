<?php

namespace App\Filament\Resources\Inventory\SerialResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\Inventory\SerialResource;

class CreateSerial extends CreateRecord
{
    protected static string $resource = SerialResource::class;
    protected function mutateFormDataBeforeCreate(array $data): array {
        foreach($data as $key=>$val){ if(is_string($val)){$data[$key] = trim($val);} }
        return $data;
    }
}
