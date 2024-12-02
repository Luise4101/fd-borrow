<?php

namespace App\Filament\Resources\Main\BorrowManageResource\Pages;

use App\Models\Asset\Kong;
use App\Models\Asset\Samnak;
use Filament\Facades\Filament;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\Main\BorrowManageResource;

class EditBorrowManage extends EditRecord
{
    protected static string $resource = BorrowManageResource::class;
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

    public function getTitle(): string {
        $record = $this->getRecord();
        $samnak = Samnak::where('qsamnak', $record->qsamnak)->first();
        $kong = Kong::where('qkong', $record->qkong)->first();
        return $samnak->csamnak.' '.$kong->ckong;
    }
}
