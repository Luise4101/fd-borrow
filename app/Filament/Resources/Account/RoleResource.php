<?php

namespace App\Filament\Resources\Account;

use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\Account\Role;
use Filament\Resources\Resource;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Section;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Enums\ActionsPosition;
use App\Filament\Resources\Account\RoleResource\Pages;
use App\Filament\Resources\Account\RoleResource\RelationManagers\UsersRelationManager;

class RoleResource extends Resource
{
    protected static ?string $model = Role::class;
    protected static ?string $modelLabel = 'บทบาท ';
    protected static ?string $navigationLabel = '1.2 บทบาท';
    protected static ?string $navigationIcon = 'heroicon-o-finger-print';

    public static function form(Form $form): Form
    {
        return $form ->schema([
            Section::make()->columns(2)->schema([
                TextInput::make('name')
                    ->hiddenLabel()
                    ->prefix(__('ชื่อบทบาท'))
                    ->required(),
                Toggle::make('active')
                    ->label(__('การใช้งาน'))
                    ->default(1)
                    ->onIcon('heroicon-m-check')
                    ->offIcon('heroicon-m-x-mark')
                    ->onColor('success')
                    ->offColor('danger')
            ])
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label(__('ID'))
                    ->toggleable()
                    ->sortable(),
                TextColumn::make('name')
                    ->label(__('ชื่อบทบาท'))
                    ->toggleable()
                    ->searchable()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label(__('วันที่สร้าง'))
                    ->toggleable()
                    ->sortable()
                    ->date('j F Y'),
                TextColumn::make('updated_at')
                    ->label(__('วันที่อัปเดต'))
                    ->toggleable()
                    ->sortable()
                    ->date('j F Y'),
                ToggleColumn::make('active')
                    ->label(__('การใช้งาน'))
                    ->toggleable()
                    ->sortable()
                    ->onIcon('heroicon-m-check')
                    ->offIcon('heroicon-m-x-mark')
                    ->onColor('success')
                    ->offColor('danger')
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make()
            ], position: ActionsPosition::BeforeCells);
    }

    public static function getRelations(): array {
        return [ UsersRelationManager::class];
    }

    public static function getPages(): array {
        return [
            'index' => Pages\ListRoles::route('/'),
            'edit' => Pages\EditRole::route('/{record}/edit')
        ];
    }
}
