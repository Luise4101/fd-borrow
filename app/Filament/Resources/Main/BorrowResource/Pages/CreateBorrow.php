<?php

namespace App\Filament\Resources\Main\BorrowResource\Pages;

use Filament\Facades\Filament;
use Illuminate\Support\Facades\Log;
use Filament\Notifications\Notification;
use App\Http\Controllers\BorrowController;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\Main\BorrowResource;

class CreateBorrow extends CreateRecord
{
    protected static string $resource = BorrowResource::class;
    protected function getRedirectUrl(): string {
        return $this->getResource()::getUrl('index');
    }
    protected function getCreatedNotification(): ?Notification {
        return Notification::make()->success()->title('Borrow created')->body('เพิ่มรายการขอใช้วิทยุสื่อสาร เรียบร้อยแล้ว');
    }
    protected function mutateFormDataBeforeCreate(array $data): array {
        foreach($data as $key=>$val){ if(is_string($val)){$data[$key] = trim($val);} }
        $data['updated_by'] = Filament::auth()->id();
        return $data;
    }
    protected function afterCreate(): void {
        $this->record->load('borrowitems');
        if($this->record->borrowitems->isNotEmpty()) {
            $controller = new BorrowController();
            $controller->genDataMail($this->record);
        } else {
            Log::warning('Email not sent: No borrowitems found');
        }
    }
}
