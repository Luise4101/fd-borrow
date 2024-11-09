<?php

namespace App\Enums;

use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum BorrowStatus: string implements HasColor, HasIcon, HasLabel
{
    case New = '8';
    case Approved = '9';
    case Noapproved = '10';
    case Delivered = '11';
    case Returned = '12';
    case Finished = '13';
    case Canceled = '14';

    public function getLabel(): string {
        return match ($this) {
            self::New => 'ขอใหม่',
            self::Approved => 'หน.กองอนุมัติ',
            self::Noapproved => 'หน.กองไม่อนุมัติ',
            self::Delivered => 'ส่งมอบของ',
            self::Returned => 'รับของคืน',
            self::Finished => 'จบงาน',
            self::Canceled => 'ยกเลิก'
        };
    }

    public function getColor(): string | array | null {
        return match ($this) {
            self::New => 'info',
            self::Returned, self::Delivered => 'warning',
            self::Approved, self::Finished => 'success',
            self::Noapproved, self::Canceled => 'danger'
        };
    }

    public function getIcon(): string {
        return match ($this) {
            self::New => 'heroicon-m-sparkles',
            self::Approved => 'heroicon-m-check-badge',
            self::Delivered => 'heroicon-m-truck',
            self::Returned => 'heroicon-m-arrow-path',
            self::Finished => 'heroicon-m-shield-check',
            self::Noapproved, self::Canceled => 'heroicon-m-x-circle'
        };
    }
}