<?php

namespace App\Filament\Resources\Main\BorrowResource\Pages;

use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use App\Models\Main\BorrowHead;
use Filament\Actions\DeleteAction;
use Illuminate\Database\Eloquent\Model;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\Main\BorrowResource;

class EditBorrow extends EditRecord
{
    protected static string $resource = BorrowResource::class;
    protected function getHeaderActions(): array { return [
        Action::make('approve')
            ->label('อนุมัติ')
            ->action(function($record) {
                $result = BorrowHead::where('id', $record->id)
                ->update([
                    'status_id' => 9,
                    'approved_at' => Carbon::now()
                ])
                ;
                if(!$result) {
                    $this->failure();
                    return;
                }
                Notification::make()->success()->title('Borrow Approved')->body('อนุมัติรายการขอใช้วิทยุสื่อสาร เรียบร้อยแล้ว')->send();
                $this->redirect($this->getResource()::getUrl('edit', ['record' => $this->record->id]));
            })
            ->visible(function($record) {
                return $record?->qhead === Filament::auth()->user()->name;
            })
            ,
        DeleteAction::make()
    ];}
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
