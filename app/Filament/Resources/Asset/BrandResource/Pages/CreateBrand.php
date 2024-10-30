<?php

namespace App\Filament\Resources\Asset\BrandResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\Asset\BrandResource;

class CreateBrand extends CreateRecord
{
    protected static string $resource = BrandResource::class;
    protected function mutateFormDataBeforeCreate(array $data): array {
        foreach ($data as $key=>$val) {if (is_string($val)) { $data[$key] = trim($val); }}
        return $data;
    }
}
