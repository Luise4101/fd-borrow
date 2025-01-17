<?php

namespace App\Filament\Resources\Main\ReturnResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\Main\ReturnResource;

class EditReturn extends EditRecord
{
    protected static string $resource = ReturnResource::class;
    protected function getHeaderActions(): array {
        return [DeleteAction::make()];
    }
}
