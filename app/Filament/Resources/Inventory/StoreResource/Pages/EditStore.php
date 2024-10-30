<?php

namespace App\Filament\Resources\Inventory\StoreResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\Inventory\StoreResource;

class EditStore extends EditRecord
{
    protected static string $resource = StoreResource::class;
    protected function getHeaderActions(): array {
        return [DeleteAction::make()];
    }
    protected function mutateFormDataBeforeSave(array $data): array {
        foreach($data as $key=>$val){ if(is_string($val)){ $data[$key] = trim($val); } }
        return $data;
    }
}
