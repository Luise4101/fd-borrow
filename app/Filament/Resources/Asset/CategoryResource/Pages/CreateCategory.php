<?php

namespace App\Filament\Resources\Asset\CategoryResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\Asset\CategoryResource;

class CreateCategory extends CreateRecord
{
    protected static string $resource = CategoryResource::class;
    protected function mutateFormDataBeforeCreate(array $data): array {
        foreach ($data as $key=>$val) {if (is_string($val)) { $data[$key] = trim($val); }}
        return $data;
    }
}
