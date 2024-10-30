<?php

namespace App\Filament\Resources\Asset\BrandResource\Pages;

use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\Asset\BrandResource;

class EditBrand extends EditRecord
{
    protected static string $resource = BrandResource::class;
    protected function mutateFormDataBeforeSave(array $data): array {
        foreach($data as $key=>$val){ if(is_string($val)){ $data[$key] = trim($val); } }
        return $data;
    }
}
