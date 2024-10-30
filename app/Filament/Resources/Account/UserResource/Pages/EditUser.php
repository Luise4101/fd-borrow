<?php

namespace App\Filament\Resources\Account\UserResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\Account\UserResource;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;
    protected function getHeaderActions(): array {
        return [ DeleteAction::make() ];
    }
    protected function getSavedNotification(): ?Notification {
        return Notification::make()->success()->title('User updated')->body('อัปเดตบทบาทของผู้ใช้ เรียบร้อยแล้ว');
    }
}
