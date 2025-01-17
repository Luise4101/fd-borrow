<?php

namespace App\Enums;

use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum ReturnStatus: string implements HasColor, HasIcon, HasLabel
{
    case Check = '19';
    case Finished = '20';

    public function getLabel(): string {
        return match($this) {
            self::Check => 'ตรวจสอบอุปกรณ์ที่คืน',
            self::Finished => 'คืนอุปกรณ์เสร็จสิ้น'
        };
    }

    public function getColor(): string | array | null {
        return match($this) {
            self::Check => 'warning',
            self::Finished => 'success'
        };
    }

    public function getIcon(): string {
        return match($this) {
            self::Check => 'heroicon-m-arrow-path',
            self::Finished => 'heroicon-m-shield-check'
        };
    }
}
