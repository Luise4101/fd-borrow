<?php

namespace App\Filament\Resources\Account\UserResource\Pages;

use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\Account\UserResource;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;
    protected function applyRoleFilter(Builder $query, string $role): Builder {
        return $query->whereHas('roles', fn($query) => $query->where('name', $role));
    }

    public function getTabs(): array {
        return [
            'all' => Tab::make(label: 'รวม'),
            'admin' => Tab::make(label: 'ผู้ดูแลระบบ')
                ->modifyQueryUsing(fn(Builder $query) => $this->applyRoleFilter($query, 'ผู้ดูแลระบบ')),
            'officer' => Tab::make(label: 'เจ้าหน้าที่')
                ->modifyQueryUsing(fn(Builder $query) => $this->applyRoleFilter($query, 'เจ้าหน้าที่')),
            'norole' => Tab::make(label: 'ผู้ยืม')
                ->modifyQueryUsing(fn(Builder $query) => $query->whereDoesntHave('roles'))
        ];
    }
    public function query(): Builder {
        return parent::query()->with('roles');
    }
}
