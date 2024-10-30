<?php

namespace App\Filament\Resources\Account\TableListResource\Pages;

use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\Account\TableListResource;

class CreateTableList extends CreateRecord
{
    protected static string $resource = TableListResource::class;
    protected function getCreatedNotification(): ?Notification {
        return Notification::make()->success()->title('Model created')->body('เพิ่มข้อมูลของรายการ เรียบร้อยแล้ว');
    }
    protected function mutateFormDataBeforeCreate(array $data): array {
        foreach ($data as $key=>$val) {if (is_string($val)) { $data[$key] = trim($val); }}
        return $data;
    }
}
