<?php

namespace App\Filament\Resources\Asset\CategoryResource\Pages;

use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\Asset\CategoryResource;

class EditCategory extends EditRecord
{
    protected static string $resource = CategoryResource::class;
    protected function mutateFormDataBeforeSave(array $data): array {
        foreach ($data as $key=>$val) {if (is_string($val)) { $data[$key] = trim($val); }}
        return $data;
    }
}
