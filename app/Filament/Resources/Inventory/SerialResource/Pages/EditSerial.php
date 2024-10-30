<?php

namespace App\Filament\Resources\Inventory\SerialResource\Pages;

use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\Inventory\SerialResource;

class EditSerial extends EditRecord
{
    protected static string $resource = SerialResource::class;
    protected function mutateFormDataBeforeSave(array $data): array {
        foreach($data as $key=>$val){ if(is_string($val)){ $data[$key] = trim($val); } }
        return $data;
    }
}
