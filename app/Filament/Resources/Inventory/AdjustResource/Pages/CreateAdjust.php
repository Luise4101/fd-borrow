<?php

namespace App\Filament\Resources\Inventory\AdjustResource\Pages;

use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\Inventory\AdjustResource;

class CreateAdjust extends CreateRecord
{
    protected static string $resource = AdjustResource::class;
    protected function getRedirectUrl(): string {
        return $this->getResource()::getUrl('index');
    }
    protected function getCreatedNotification(): ?Notification {
        return Notification::make()->success()->title('AdjustHead created')->body('เพิ่มการปรับยอดคลังอุปกรณ์ เรียบร้อยแล้ว');
    }
    protected function mutateFormDataBeforeCreate(array $data): array {
        foreach($data as $key=>$val){ if(is_string($val)){$data[$key] = trim($val);} }
        $data['created_by'] = Filament::auth()->id();
        return $data;
    }
}
