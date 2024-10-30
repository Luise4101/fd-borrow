<?php

namespace App\Filament\Resources\Account\TableListResource\Pages;

use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\Account\TableListResource;

class EditTableList extends EditRecord
{
    protected static string $resource = TableListResource::class;
    protected function getSavedNotification(): ?Notification {
        return Notification::make()->success()->title('Model updated')->body('อัปเดตข้อมูลของรายการ เรียบร้อยแล้ว');
    }
    protected function mutateFormDataBeforeSave(array $data): array {
        foreach($data as $key=>$val){ if(is_string($val)){ $data[$key] = trim($val); } }
        return $data;
    }
}
