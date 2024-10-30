<?php

namespace App\Filament\Resources\Inventory\RepairResource\Pages;

use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\Inventory\RepairResource;

class CreateRepair extends CreateRecord
{
    protected static string $resource = RepairResource::class;
    protected function getRedirectUrl(): string {
        return $this->getResource()::getUrl('index');
    }
    protected function getCreatedNotification(): ?Notification {
        return Notification::make()->success()->title('Repair created')->body('เพิ่มการส่งซ่อมอุปกรณ์ เรียบร้อยแล้ว');
    }
    protected function mutateFormDataBeforeCreate(array $data): array {
        foreach($data as $key=>$val){ if(is_string($val)){$data[$key] = trim($val);} }
        $data['created_by'] = Filament::auth()->id();
        return $data;
    }
}
