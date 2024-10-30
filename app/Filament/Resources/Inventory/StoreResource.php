<?php

namespace App\Filament\Resources\Inventory;

use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\Inventory\Store;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Enums\ActionsPosition;
use App\Filament\Resources\Inventory\StoreResource\Pages\ListStores;

class StoreResource extends Resource
{
    protected static ?string $model = Store::class;
    protected static ?string $modelLabel = 'คลังอุปกรณ์ ';
    protected static ?string $navigationLabel = '3.4 คลังอุปกรณ์';
    protected static ?string $navigationIcon = 'heroicon-o-home-modern';
    protected static ?string $activeNavigationIcon = 'heroicon-s-home-modern';

    public static function form(Form $form): Form
    {
        return $form ->schema([
            Section::make()->columns(2)->schema([
                Select::make('product_id')
                    ->hiddenLabel()
                    ->prefix(__('อุปกรณ์/รุ่น'))
                    ->relationship('product', 'name')
                    ->disabled(),
                TextInput::make('q_all')
                    ->hiddenLabel()
                    ->prefix(__('จำนวนทั้งหมด'))
                    ->numeric(),
                TextInput::make('q_waste')
                    ->hiddenLabel()
                    ->prefix(__('จำนวนที่เสีย'))
                    ->numeric(),
                TextInput::make('q_borrow')
                    ->hiddenLabel()
                    ->prefix(__('จำนวนที่ยืม'))
                    ->numeric(),
                TextInput::make('q_book')
                    ->hiddenLabel()
                    ->prefix(__('จำนวนที่จอง'))
                    ->numeric()
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
                TextColumn::make('product.id')
                    ->label(__('ID อุปกรณ์'))
                    ->toggleable(isToggledHiddenByDefault:true)
                    ->searchable()
                    ->sortable(),
                TextColumn::make('product.name')
                    ->label(__('อุปกรณ์/รุ่น'))
                    ->toggleable()
                    ->searchable()
                    ->sortable(),
                TextColumn::make('q_all')
                    ->label(__('จำนวนทั้งหมด'))
                    ->numeric(decimalPlaces: 0,decimalSeparator: '.',thousandsSeparator: ',')
                    ->toggleable()
                    ->searchable()
                    ->sortable(),
                TextColumn::make('q_waste')
                    ->label(__('จำนวนที่เสีย'))
                    ->numeric(decimalPlaces: 0,decimalSeparator: '.',thousandsSeparator: ',')
                    ->toggleable()
                    ->searchable()
                    ->sortable(),
                TextColumn::make('q_book')
                    ->label(__('จำนวนที่จอง'))
                    ->numeric(decimalPlaces: 0,decimalSeparator: '.',thousandsSeparator: ',')
                    ->toggleable()
                    ->searchable()
                    ->sortable(),
                TextColumn::make('q_borrow')
                    ->label(__('จำนวนที่ยืม'))
                    ->numeric(decimalPlaces: 0,decimalSeparator: '.',thousandsSeparator: ',')
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
                    ->date('j F Y')
            ])
            ->actions([EditAction::make()], position: ActionsPosition::BeforeCells)
            ->defaultPaginationPageOption(50);
    }

    public static function getPages(): array {
        return ['index' => ListStores::route('/')];
    }
}
