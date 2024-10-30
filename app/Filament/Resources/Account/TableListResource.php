<?php

namespace App\Filament\Resources\Account;

use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use App\Models\Account\TableList;
use Filament\Forms\Components\Section;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Enums\ActionsPosition;
use App\Filament\Resources\Account\TableListResource\Pages;

class TableListResource extends Resource
{
    protected static ?string $model = TableList::class;
    protected static ?string $modelLabel = 'รายการข้อมูล ';
    protected static ?string $navigationLabel = '1.3 รายการข้อมูล';
    protected static ?string $navigationIcon = 'heroicon-o-table-cells';
    protected static ?string $activeNavigationIcon = 'heroicon-s-table-cells';

    public static function form(Form $form): Form
    {
        return $form ->schema([
            Section::make()->schema([
                TextInput::make('name')
                    ->hiddenLabel()
                    ->prefix(__('ชื่อข้อมูล'))
                    ->required()
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
                    ->date('j F Y')
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ], position: ActionsPosition::BeforeCells)
            ->defaultPaginationPageOption(25);
    }

    public static function getPages(): array {
        return [ 'index' => Pages\ListTableLists::route('/') ];
    }
}
