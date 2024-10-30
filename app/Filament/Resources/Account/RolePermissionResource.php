<?php

namespace App\Filament\Resources\Account;

use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Tables\Grouping\Group;
use App\Models\Account\RolePermission;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use App\Filament\Resources\Account\RolePermissionResource\Pages;

class RolePermissionResource extends Resource
{
    protected static ?string $modelLabel = '';
    protected static ?string $model = RolePermission::class;
    protected static ?string $navigationLabel = '1.5 ตั้งค่าสิทธิ์การเข้าถึง';
    protected static ?string $navigationIcon = 'heroicon-o-credit-card';
    protected static ?string $activeNavigationIcon = 'heroicon-s-credit-card';

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('role.name')
                    ->label(__('บทบาท'))
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault:true),
                TextColumn::make('permission.name')
                    ->label(__('สิทธิ์การเข้าถึง'))
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                ToggleColumn::make('is_read')
                    ->label(__('การอ่าน'))
                    ->onIcon('heroicon-m-check')
                    ->offIcon('heroicon-m-x-mark')
                    ->onColor('success')
                    ->offColor('danger'),
                ToggleColumn::make('is_create')
                    ->label(__('การเพิ่ม'))
                    ->onIcon('heroicon-m-check')
                    ->offIcon('heroicon-m-x-mark')
                    ->onColor('success')
                    ->offColor('danger'),
                ToggleColumn::make('is_update')
                    ->label(__('การแก้ไข'))
                    ->onIcon('heroicon-m-check')
                    ->offIcon('heroicon-m-x-mark')
                    ->onColor('success')
                    ->offColor('danger'),
                ToggleColumn::make('is_delete')
                    ->label(__('การลบ'))
                    ->onIcon('heroicon-m-check')
                    ->offIcon('heroicon-m-x-mark')
                    ->onColor('success')
                    ->offColor('danger'),
                ToggleColumn::make('active')
                    ->label(__('การใช้งาน'))
                    ->toggleable()
                    ->onIcon('heroicon-m-check')
                    ->offIcon('heroicon-m-x-mark')
                    ->onColor('success')
                    ->offColor('danger'),
                TextColumn::make('created_at')
                    ->label(__('วันที่สร้าง'))
                    ->toggleable(isToggledHiddenByDefault:true)
                    ->searchable()
                    ->sortable()
                    ->date('j F Y'),
                TextColumn::make('updated_at')
                    ->label(__('วันที่อัปเดต'))
                    ->toggleable(isToggledHiddenByDefault:true)
                    ->searchable()
                    ->sortable()
                    ->date('j F Y')
            ])
            ->defaultGroup('role.name')->groups([
                Group::make('role.name')->collapsible()->label('บทบาท'),
                Group::make('permission.name')->collapsible()->label('สิทธิ์การเข้าถึง')
            ])
            ->defaultPaginationPageOption(50);
    }

    public static function getPages(): array {
        return [ 'index' => Pages\ListRolePermissions::route('/') ];
    }
}
