<?php

namespace App\Filament\Resources\Asset;

use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\Asset\Status;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Section;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Enums\ActionsPosition;
use App\Filament\Resources\Asset\StatusResource\Pages;

class StatusResource extends Resource
{
    protected static ?string $model = Status::class;
    protected static ?string $modelLabel = 'สถานะ ';
    protected static ?string $navigationLabel = '2.1 สถานะ';
    protected static ?string $navigationIcon = 'heroicon-o-clock';
    protected static ?string $activeNavigationIcon = 'heroicon-s-clock';

    public static function form(Form $form): Form
    {
        return $form ->schema([
            Section::make()->columns(7)->schema([
                TextInput::make('name')
                    ->label(__('ชื่อสถานะ'))
                    ->maxLength(100)
                    ->required()
                    ->columnSpan(3),
                Select::make('table_list_id')
                    ->label(__('รายการข้อมูล'))
                    ->relationship('table_list', 'name')
                    ->native(false)
                    ->required()
                    ->columnSpan(3),
                Toggle::make('active')
                    ->label(__('การใช้งาน'))
                    ->default(1)
                    ->inline(false)
                    ->onIcon('heroicon-m-check')
                    ->offIcon('heroicon-m-x-mark')
                    ->onColor('success')
                    ->offColor('danger')
                    ->columnSpan(1)
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
                    ->searchable()
                    ->sortable(),
                TextColumn::make('name')
                    ->label(__('ชื่อสถานะ'))
                    ->toggleable()
                    ->searchable()
                    ->sortable(),
                TextColumn::make('table_list.name')
                    ->label(__('ชื่อข้อมูล'))
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
            ->filters([
                SelectFilter::make('table_list')
                    ->label(__('รายการข้อมูล'))
                    ->relationship('table_list', 'name')
                    ->native(false)
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make()
            ], position: ActionsPosition::BeforeCells)
            ->defaultPaginationPageOption(25);
    }

    public static function getPages(): array {
        return [ 'index' => Pages\ListStatuses::route('/') ];
    }
}
