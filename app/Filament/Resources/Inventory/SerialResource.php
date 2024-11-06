<?php

namespace App\Filament\Resources\Inventory;

use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use App\Models\Inventory\Serial;
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
use App\Filament\Resources\Inventory\SerialResource\Pages;

class SerialResource extends Resource
{
    protected static ?string $model = Serial::class;
    protected static ?string $modelLabel = 'หมายเลขวิทยุสื่อสาร ';
    protected static ?string $navigationLabel = '3.2 หมายเลขวิทยุสื่อสาร';
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar-square';
    protected static ?string $activeNavigationIcon = 'heroicon-s-chart-bar-square';

    public static function form(Form $form): Form
    {
        $product = Serial::latest()->first()->product_id;
        $serialname = Serial::latest()->first()->name;
        preg_match('/\d+/', $serialname, $matches);
        $numericPart = (int)$matches[0];
        $newNumericPart = $numericPart + 1;
        $newSerialname = preg_replace_callback('/\d+/', function($matches) use ($newNumericPart) {
            return str_pad($newNumericPart, strlen($matches[0]), '0', STR_PAD_LEFT);
        }, $serialname);

        return $form ->schema([
            Section::make()
                ->columns(2)
                ->schema([
                    Select::make('product_id')
                        ->hiddenLabel()
                        ->prefix(__('อุปกรณ์/รุ่น'))
                        ->relationship('product', 'name')
                        ->native(false)
                        ->default($product)
                        ->required(),
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
                        ->default(1)
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
                    ->searchable()
                    ->sortable(),
                TextColumn::make('name')
                    ->label(__('หมายเลข'))
                    ->toggleable()
                    ->searchable()
                    ->sortable(),
                TextColumn::make('product.name')
                    ->label(__('อุปกรณ์/รุ่น'))
                    ->toggleable()
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
                            'เลยกำหนดคืน' => 'danger'
                        };
                    }),
                TextColumn::make('serial_number')
                    ->label(__('รหัส Serial'))
                    ->toggleable()
                    ->searchable()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label(__('วันที่สร้าง'))
                    ->toggleable(isToggledHiddenByDefault:true)
                    ->sortable()
                    ->date('j F Y'),
                TextColumn::make('updated_at')
                    ->label(__('วันที่อัปเดต'))
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
            ->defaultSort('id', 'desc')
            ->filters([
                SelectFilter::make('product')
                    ->label(__('อุปกรณ์/รุ่น'))
                    ->relationship('product', 'name')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('status')
                    ->label(__('สถานะ'))
                    ->relationship('status', 'name')
                    ->searchable()
                    ->preload()
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make()
            ], position: ActionsPosition::BeforeCells)
            ->defaultPaginationPageOption(25);
    }

    public static function getPages(): array {
        return ['index' => Pages\ListSerials::route('/')];
    }
}
