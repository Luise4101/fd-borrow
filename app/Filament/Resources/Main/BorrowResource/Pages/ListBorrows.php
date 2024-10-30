<?php

namespace App\Filament\Resources\Main\BorrowResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\Main\BorrowResource;

class ListBorrows extends ListRecords
{
    protected static string $resource = BorrowResource::class;
    protected function getHeaderActions(): array {
        return [
            CreateAction::make()->label(__('ขอใช้วิทยุสื่อสาร'))
        ];
    }
}
