<?php

namespace App\Filament\Resources\Asset;

use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\Asset\Category;
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
use App\Filament\Resources\Asset\CategoryResource\Pages;

class CategoryResource extends Resource
{
    protected static ?string $modelLabel = 'ประเภท ';
    protected static ?string $model = Category::class;
    protected static ?string $navigationLabel = '2.2 ประเภท';
    protected static ?string $navigationIcon = 'heroicon-o-squares-2x2';
    protected static ?string $activeNavigationIcon = 'heroicon-s-squares-2x2';

    public static function form(Form $form): Form
    {
        return $form ->schema([
            Section::make()->columns(7)->schema([
                TextInput::make('name')
                    ->label(__('ชื่อประเภท'))
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
                    ->label(__('ชื่อประเภท'))
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
        return [ 'index' => Pages\ListCategories::route('/') ];
    }
}
