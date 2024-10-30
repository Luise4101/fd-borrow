<?php

namespace App\Filament\Resources\Asset\SupplierResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\Asset\SupplierResource;

class EditSupplier extends EditRecord
{
    protected static string $resource = SupplierResource::class;
    protected function getHeaderActions(): array{
        return [ DeleteAction::make() ];
    }
    protected function getRedirectUrl(): string{
        return $this->getResource()::getUrl('index');
    }
    protected function getSavedNotification(): ?Notification {
        return Notification::make()
            ->success()
            ->title('Supplier updated')
            ->body('อัปเดตข้อมูลร้าน เรียบร้อยแล้ว');
    }
    protected function mutateFormDataBeforeSave(array $data): array {
        foreach($data as $key=>$val){ if(is_string($val)){ $data[$key] = trim($val); } }
        return $data;
    }
}
