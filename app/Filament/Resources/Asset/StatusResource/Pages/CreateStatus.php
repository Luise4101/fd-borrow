<?php

namespace App\Filament\Resources\Asset\StatusResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\Asset\StatusResource;

class CreateStatus extends CreateRecord
{
    protected static string $resource = StatusResource::class;
    protected function mutateFormDataBeforeCreate(array $data): array {
        foreach ($data as $key=>$val) {if (is_string($val)) { $data[$key] = trim($val); }}
        return $data;
    }
}
