<?php

namespace App\Filament\Resources\Main\BorrowResource\Pages;

use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use App\Models\Main\BorrowHead;
use Filament\Actions\DeleteAction;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use App\Http\Controllers\BorrowController;
use App\Filament\Resources\Main\BorrowResource;

class EditBorrow extends EditRecord
{
    protected static string $resource = BorrowResource::class;
    protected function getHeaderActions(): array { return [
        Action::make('approve')
            ->label('อนุมัติ')
            ->action(function(BorrowHead $record): void {
                $record->status_id = 9;
                $record->approved_at = now();
                if(!$record->save()) {
                    Notification::make()->danger()->title('Approve Failed')->body('การอนุมัติรายการขอใช้ผิดพลาด โปรดติดต่อเจ้าหน้าที่')->send();
                    return;
                } else {
                    Notification::make()->success()->title('Borrow Approved')->body('อนุมัติรายการขอใช้วิทยุสื่อสาร เรียบร้อยแล้ว')->send();
                    $this->redirect($this->getResource()::getUrl('edit', ['record' => $this->record->id]));
                }
            })
            ->visible(function($record) {
                return $record->status_id == 8 && $record?->qhead === Filament::auth()->user()->name;
            }),
        Action::make('disapprove')
            ->label('ไม่อนุมัติ')
            ->color('danger')
            ->action(function(BorrowHead $record): void {
                $record->status_id = 10;
                $record->approved_at = now();
                if(!$record->save()) {
                    Notification::make()->danger()->title('Approve Failed')->body('การอนุมัติรายการขอใช้ผิดพลาด โปรดติดต่อเจ้าหน้าที่')->send();
                    return;
                } else {
                    Notification::make()->danger()->title('Borrow Disapproved')->body('ไม่อนุมัติรายการขอใช้วิทยุสื่อสาร เรียบร้อยแล้ว')->send();
                    $this->redirect($this->getResource()::getUrl('edit', ['record' => $this->record->id]));
                }
            })
            ->visible(function($record) {
                return $record->status_id == 8 && $record?->qhead === Filament::auth()->user()->name;
            }),
        DeleteAction::make()
            ->hidden(function($record) {
                return $record->status_id != 8;
            })
            ->visible(function($record) {
                return $record?->qhead !== Filament::auth()->user()->name;
            })
    ]; }
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
    protected function afterSave(): void {
        $this->record->load('borrowitems');
        if($this->record->borrowitems->isNotEmpty()) {
            $controller = new BorrowController();
            $controller->genDataMail($this->record);
        } else {
            Log::warning('Email not sent: No borrowitems found');
        }
    }
}
