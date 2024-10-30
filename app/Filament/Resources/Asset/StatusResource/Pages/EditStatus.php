<?php

namespace App\Filament\Resources\Asset\StatusResource\Pages;

use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\Asset\StatusResource;

class EditStatus extends EditRecord
{
    protected static string $resource = StatusResource::class;
    protected function mutateFormDataBeforeSave(array $data): array {
        foreach ($data as $key=>$val) {if (is_string($val)) { $data[$key] = trim($val); }}
        return $data;
    }
}
