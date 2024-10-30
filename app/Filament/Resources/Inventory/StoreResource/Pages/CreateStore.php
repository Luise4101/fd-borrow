<?php

namespace App\Filament\Resources\Inventory\StoreResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\Inventory\StoreResource;

class CreateStore extends CreateRecord
{
    protected static string $resource = StoreResource::class;
    protected function mutateFormDataBeforeCreate(array $data): array {
        foreach($data as $key=>$val){ if(is_string($val)){$data[$key] = trim($val);} }
        return $data;
    }
}
