<?php
namespace App\Filament\Resources\Main\BorrowResource\Pages;

use Filament\Resources\Pages\ViewRecord;
use Illuminate\Contracts\Support\Htmlable;
use App\Filament\Resources\Main\BorrowResource;

class ViewBorrow extends ViewRecord {
    protected static string $resource = BorrowResource::class;

    public function getTitle(): string | Htmlable {
        $record = $this->getRecord();
        return "Borrow ID ".$record->id;
    }
}