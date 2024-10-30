<?php

namespace App\Filament\Resources\Account;

use App\Models\User;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Enums\ActionsPosition;
use App\Filament\Resources\Account\UserResource\Pages;

class UserResource extends Resource
{
    protected static ?string $model = User::class;
    protected static ?string $modelLabel = '';
    protected static ?string $navigationLabel = '1.1 ข้อมูลผู้ใช้';
    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $activeNavigationIcon = 'heroicon-s-users';

    public static function form(Form $form): Form
    {
        return $form ->schema([
            Section::make()->columns(2)->schema([
                TextInput::make('name')
                    ->hiddenLabel()
                    ->prefix(__('User หน้าแดง'))
                    ->disabled(),
                TextInput::make('email')
                    ->hiddenLabel()
                    ->prefix(__('อีเมล'))
                    ->disabled(),
                TextInput::make('fullname')
                    ->hiddenLabel()
                    ->prefix(__('ชื่อเต็ม'))
                    ->disabled(),
                Select::make('roles')
                    ->hiddenLabel()
                    ->prefix(__('บทบาท'))
                    ->relationship('roles', 'name')
                    ->multiple()
                    ->native(false)
                    ->preload()
            ])
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label(__('ID'))
                    ->toggleable(isToggledHiddenByDefault:true)
                    ->sortable(),
                TextColumn::make('name')
                    ->label(__('User หน้าแดง'))
                    ->toggleable()
                    ->searchable()
                    ->sortable(),
                TextColumn::make('fullname')
                    ->label(__('ชื่อเต็ม'))
                    ->toggleable()
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email')
                    ->label(__('อีเมล'))
                    ->toggleable()
                    ->searchable()
                    ->sortable(),
                TextColumn::make('roles.name')
                    ->label(__('บทบาท'))
                    ->badge()
                    ->toggleable()
                    ->searchable()
                    ->sortable()
                    ->color(function(string $state): string {
                        return match($state) {
                            'ผู้ดูแลระบบ' => 'info',
                            'เจ้าหน้าที่' => 'warning'
                        };
                    }),
                TextColumn::make('created_at')
                    ->label(__('ล็อกอินครั้งแรก'))
                    ->toggleable(isToggledHiddenByDefault:true)
                    ->sortable()
                    ->date('j F Y'),
                TextColumn::make('updated_at')
                    ->label(__('ล็อกอินล่าสุด'))
                    ->toggleable()
                    ->sortable()
                    ->date('j F Y')
            ])
            ->filters([
                SelectFilter::make('roles')->relationship('roles', 'name')->label(__('Role'))
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make()
            ], position: ActionsPosition::BeforeCells)
        ;
    }

    public static function getPages(): array {
        return [ 'index' => Pages\ListUsers::route('/') ];
    }
}
