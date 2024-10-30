<?php

namespace App\Filament\Resources\Asset\SupplierResource\Pages;

use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\Asset\SupplierResource;

class CreateSupplier extends CreateRecord
{
    protected static string $resource = SupplierResource::class;
    protected function getRedirectUrl(): string {
        return $this->getResource()::getUrl('index');
    }
    protected function getCreatedNotification(): ?Notification {
        return Notification::make()
            ->success()
            ->title('Supplier created')
            ->body('สร้างข้อมูลร้านใหม่ เรียบร้อยแล้ว');
    }
    protected function mutateFormDataBeforeCreate(array $data): array {
        foreach($data as $key=>$val) { if(is_string($val)) { $data[$key] = trim($val); } }
        return $data;
    }
}
