<?php
namespace App\Filament\Resources\Main\BorrowResource\Pages;

use Filament\Resources\Pages\ViewRecord;
use App\Filament\Resources\Main\BorrowResource;

class ViewBorrow extends ViewRecord {
    protected static string $resource = BorrowResource::class;

    public function getTitle(): string {
        return false;
    }
}