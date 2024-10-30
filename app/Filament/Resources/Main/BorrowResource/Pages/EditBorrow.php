<?php

namespace App\Filament\Resources\Main\BorrowResource\Pages;

use Filament\Facades\Filament;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\Main\BorrowResource;

class EditBorrow extends EditRecord
{
    protected static string $resource = BorrowResource::class;
    protected function getHeaderActions(): array {
        return [DeleteAction::make()];
    }
    protected function getRedirectUrl(): string {
        return $this->getResource()::getUrl('index');
    }
    protected function getSavedNotification(): ?Notification {
        return Notification::make()->success()->title('Borrow updated')->body('อัปเดตรายการขอใช้วิทยุสื่อสาร เรียบร้อยแล้ว');
    }
    protected function mutateFormDataBeforeSave(array $data): array {
        foreach($data as $key=>$val){ if(is_string($val)){ $data[$key] = trim($val); } }
        $data['updated_by'] = Filament::auth()->id();
        return $data;
    }
}
