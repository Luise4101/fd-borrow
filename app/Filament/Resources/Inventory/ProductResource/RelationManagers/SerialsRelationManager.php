<?php

namespace App\Filament\Resources\Inventory\ProductResource\RelationManagers;

use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use App\Models\Inventory\Serial;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Section;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Enums\ActionsPosition;
use Filament\Resources\RelationManagers\RelationManager;

class SerialsRelationManager extends RelationManager
{
    protected static string $relationship = 'serials';

    public function form(Form $form): Form
    {
        $serialname = Serial::latest()->first()->name;
        preg_match('/\d+/', $serialname, $matches);
        $numericPart = (int)$matches[0];
        $newNumericPart = $numericPart + 1;
        $newSerialname = preg_replace_callback('/\d+/', function($matches) use ($newNumericPart) {
            return str_pad($newNumericPart, strlen($matches[0]), '0', STR_PAD_LEFT);
        }, $serialname);

        return $form ->schema([
            Section::make()->columns(2)->schema([
                Select::make('status_id')
                    ->hiddenLabel()
                    ->prefix(__('สถานะ'))
                    ->relationship('status', 'name')
                    ->native(false)
                    ->default(1)
                    ->required(),
                TextInput::make('name')
                    ->hiddenLabel()
                    ->prefix(__('หมายเลข'))
                    ->default($newSerialname)
                    ->required(),
                TextInput::make('serial_number')
                    ->hiddenLabel()
                    ->prefix(__('รหัส Serial'))
                    ->maxLength(20)
                    ->default(Str::random(10))
                    ->required(),
                Toggle::make('license')
                    ->label(__('มีใบอนุญาติขอใช้วิทยุสื่อสาร'))
                    ->onIcon('heroicon-m-check')
                    ->offIcon('heroicon-m-x-mark')
                    ->onColor('success')
                    ->offColor('danger')
            ])
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('id')
                    ->label(__('Serial ID'))
                    ->toggleable(isToggledHiddenByDefault:true)
                    ->searchable()
                    ->sortable(),
                TextColumn::make('status.name')
                    ->label(__('สถานะ'))
                    ->toggleable()
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color(function(string $state): string {
                        return match($state) {
                            'พร้อมใช้งาน' => 'success',
                            'ถูกจอง' => 'warning',
                            'ถูกยืม' => 'info',
                            'ชำรุด' => 'danger',
                            'ส่งซ่อม' => 'primary',
                            'ถึงกำหนดคืน' => 'warning',
                            'คืนล่าช้า' => 'danger'
                        };
                    }),
                TextColumn::make('name')
                    ->label(__('หมายเลข'))
                    ->toggleable()
                    ->searchable()
                    ->sortable(),
                TextColumn::make('serial_number')
                    ->label(__('รหัส Serial'))
                    ->toggleable()
                    ->searchable()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label(__('สร้างเมื่อ'))
                    ->toggleable(isToggledHiddenByDefault:true)
                    ->sortable()
                    ->date('j F Y'),
                TextColumn::make('updated_at')
                    ->label(__('อัปเดตเมื่อ'))
                    ->toggleable(isToggledHiddenByDefault:true)
                    ->sortable()
                    ->date('j F Y'),
                ToggleColumn::make('license')
                    ->label(__('ใบอนุญาติ'))
                    ->toggleable()
                    ->sortable()
                    ->onIcon('heroicon-m-check')
                    ->offIcon('heroicon-m-x-mark')
                    ->onColor('success')
                    ->offColor('danger')
            ])
            ->defaultSort('name', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->label(__('สถานะ'))
                    ->relationship('status', 'name')
                    ->searchable()
                    ->preload()
            ])
            ->headerActions([CreateAction::make()])
            ->actions([
                EditAction::make(),
                DeleteAction::make()
            ], position: ActionsPosition::BeforeCells);
    }
}
