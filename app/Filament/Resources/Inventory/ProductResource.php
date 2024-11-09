<?php

namespace App\Filament\Resources\Inventory;

use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use App\Models\Inventory\Product;
use Illuminate\Http\UploadedFile;
use Filament\Forms\Components\Select;
use Filament\Infolists\Components\Grid;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\Group;
use Filament\Infolists\Components\Split;
use Filament\Tables\Columns\ImageColumn;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Enums\ActionsPosition;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Forms\Components\Section as FormSection;
use App\Filament\Resources\Inventory\ProductResource\Pages;
use Filament\Infolists\Components\Section as InfolistSection;
use App\Filament\Resources\Inventory\ProductResource\RelationManagers\SerialsRelationManager;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;
    protected static ?string $modelLabel = 'ข้อมูลอุปกรณ์ ';
    protected static ?string $navigationLabel = '3.1 ข้อมูลอุปกรณ์';
    protected static ?string $navigationIcon = 'heroicon-o-cube';
    protected static ?string $activeNavigationIcon = 'heroicon-s-cube';

    public static function form(Form $form): Form
    {
        return $form ->columns(3)->schema([
            FormSection::make()
                ->columnSpan(['lg' => 2])
                ->columns(2)
                ->schema([
                    Select::make('category_id')
                        ->hiddenLabel()
                        ->prefix(__('ประเภทอุปกรณ์'))
                        ->relationship('category', 'name')
                        ->native(false)
                        ->required(),
                    Select::make('brand_id')
                        ->hiddenLabel()
                        ->prefix(__('แบรนด์อุปกรณ์'))
                        ->relationship('brand', 'name')
                        ->native(false),
                    TextInput::make('name')
                        ->hiddenLabel()
                        ->prefix(__('ชื่ออุปกรณ์/รุ่น'))
                        ->required(),
                    FileUpload::make('img')
                        ->label(__('ภาพอุปกรณ์'))
                        ->directory('images/product')
                        ->imageEditor()
                        ->imageEditorAspectRatios([null,'16:9','4:3','1:1'])
                        ->getUploadedFileNameForStorageUsing(fn(UploadedFile $file) => Date('YmdHis').'_Product_'.Str::random(5).'.'.$file->getClientOriginalExtension())
                        ->multiple()
                        ->openable()
                        ->reorderable()
                        ->appendFiles()
                        ->columnSpanFull()
                ]),
            FormSection::make()
                ->columnSpan(['lg' => 1])
                ->columns(1)
                ->schema([
                    TextInput::make('price_product')
                        ->hiddenLabel()
                        ->prefix(__('ราคาอุปกรณ์'))
                        ->numeric()
                        ->required(),
                    TextInput::make('price_borrow')
                        ->hiddenLabel()
                        ->prefix(__('ค่ามัดจำ'))
                        ->numeric()
                        ->default(0)
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
                TextColumn::make('category.name')
                    ->label(__('ประเภทอุปกรณ์'))
                    ->toggleable()
                    ->searchable()
                    ->sortable(),
                TextColumn::make('brand.name')
                    ->label(__('แบรนด์อุปกรณ์'))
                    ->toggleable()
                    ->searchable()
                    ->sortable(),
                TextColumn::make('name')
                    ->label(__('ชื่ออุปกรณ์/รุ่น'))
                    ->toggleable()
                    ->searchable()
                    ->sortable(),
                ImageColumn::make('img')
                    ->label(__('ภาพอุปกรณ์'))
                    ->stacked()
                    ->toggleable(),
                TextColumn::make('price_product')
                    ->label(__('ราคาอุปกรณ์'))
                    ->numeric(decimalPlaces:0,decimalSeparator:'.',thousandsSeparator:',')
                    ->toggleable()
                    ->sortable(),
                TextColumn::make('price_borrow')
                    ->label(__('ค่ามัดจำ'))
                    ->numeric(decimalPlaces:0,decimalSeparator:'.',thousandsSeparator:',')
                    ->toggleable()
                    ->sortable(),
                TextColumn::make('creater.name')
                    ->label(__('สร้างโดย'))
                    ->toggleable(isToggledHiddenByDefault:true)
                    ->searchable()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label(__('สร้างเมื่อ'))
                    ->toggleable(isToggledHiddenByDefault:true)
                    ->sortable()
                    ->date('j F Y'),
                TextColumn::make('updater.name')
                    ->label(__('อัปเดตโดย'))
                    ->toggleable(isToggledHiddenByDefault:true)
                    ->searchable()
                    ->sortable(),
                TextColumn::make('updated_at')
                    ->label(__('อัปเดตเมื่อ'))
                    ->toggleable()
                    ->sortable()
                    ->date('j F Y')
            ])
            ->defaultSort('id', 'desc')
            ->filters([
                SelectFilter::make('category')
                    ->label(__('ประเภทอุปกรณ์'))
                    ->relationship('category', 'name')
                    ->native(false)
            ])
            ->actions([
                ViewAction::make()->openUrlInNewTab(),
                EditAction::make(),
                DeleteAction::make()
            ], position: ActionsPosition::BeforeCells)
            ->defaultPaginationPageOption(50);
    }

    public static function getRelations(): array {
        return [SerialsRelationManager::class];
    }

    public static function getPages(): array {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
            'view' => Pages\ViewProduct::route('/{record}')
        ];
    }

    public static function infolist(Infolist $infolist): Infolist {
        return $infolist->schema([
            InfolistSection::make()->schema([
                Split::make([
                    Grid::make(2)->schema([
                        Group::make([
                            TextEntry::make('category.name')
                                ->label('ประเภทอุปกรณ์'),
                            TextEntry::make('brand.name')
                                ->label('แบรนด์อุปกรณ์')
                        ]),
                        Group::make([
                            TextEntry::make('price_product')
                                ->label('ราคาอุปกรณ์')
                                ->formatStateUsing(fn($record) => self::formatPrice($record->price_product)),
                            TextEntry::make('price_borrow')
                                ->label('ค่ามัดจำ')
                                ->formatStateUsing(fn($record) => self::formatPrice($record->price_borrow))
                        ])
                    ]),
                    ImageEntry::make('img')->hiddenLabel()->grow(false),
                ])->from('lg')
            ])
        ]);
    }

    public static function formatPrice($price) {
        return number_format($price, $price == (int)$price ? 0 : 2);
    }
}
