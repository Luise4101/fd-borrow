<?php

namespace App\Filament\Resources\Inventory;

use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\Inventory\Store;
use App\Models\Inventory\Serial;
use Filament\Resources\Resource;
use App\Models\Inventory\Product;
use Awcodes\TableRepeater\Header;
use App\Models\Inventory\AdjustHead;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Select;
use Filament\Support\Enums\Alignment;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Filters\SelectFilter;
use Filament\Forms\Components\Placeholder;
use Filament\Tables\Enums\ActionsPosition;
use Filament\Forms\Components\Actions\Action;
use Filament\Tables\Actions\DeleteBulkAction;
use Awcodes\TableRepeater\Components\TableRepeater;
use App\Filament\Resources\Inventory\ProductResource;
use App\Filament\Resources\Inventory\AdjustResource\Pages\EditAdjust;
use App\Filament\Resources\Inventory\AdjustResource\Pages\ListAdjusts;
use App\Filament\Resources\Inventory\AdjustResource\Pages\CreateAdjust;

class AdjustResource extends Resource
{
    protected static ?string $model = AdjustHead::class;
    protected static ?string $modelLabel = 'การปรับยอดคงคลัง ';
    protected static ?string $navigationLabel = '3.3 การปรับยอดคงคลัง';
    protected static ?string $navigationIcon = 'heroicon-o-adjustments-horizontal';
    protected static ?string $activeNavigationIcon = 'heroicon-s-adjustments-horizontal';

    public static function form(Form $form): Form
    {
        return $form ->schema([
            Group::make()->schema([
                Section::make()->schema([
                    Select::make('category_id')
                        ->hiddenLabel()
                        ->prefix(__('ประเภทปรับยอด'))
                        ->relationship('category','name')
                        ->native(false)
                        ->reactive()
                        ->required(),
                    Select::make('supplier_id')
                        ->hiddenLabel()
                        ->prefix(__('ร้าน/หน่วยงาน'))
                        ->relationship('supplier','name')
                        ->native(false),
                    DatePicker::make('purchase_at')
                        ->hiddenLabel()
                        ->prefix(__('วันที่จัดซื้อ'))
                        ->native(false)
                        ->displayFormat('j F Y')
                        ->reactive()
                        ->disabled(fn (Get $get) => $get('category_id') != 1)
                        ->required(fn (Get $get) => $get('category_id') == 1),
                    Textarea::make('note')
                        ->label(__('รายละเอียดเพิ่มเติม'))
                        ->placeholder(__('ข้อมูลเพิ่มเติมเกี่ยวกับการปรับคลังสินค้า'))
                        ->autosize()
                        ->maxLength(500)
                        ->columnSpanfull()
                ])->columns(3),
                Section::make()->schema([ static::getAdjustitemsRepeater() ])
            ])->columnSpan(['lg' => fn(?AdjustHead $record) => $record === null ? 4 : 3]),
            Group::make()->schema([
                Section::make()->schema([
                    Placeholder::make('created_at')
                        ->label(__('สร้างเมื่อ'))
                        ->inlineLabel()
                        ->content(fn(AdjustHead $record): ?string => $record->created_at?->diffForHumans()),
                    Placeholder::make('creater.name')
                        ->label(__('โดย'))
                        ->inlineLabel()
                        ->content(fn(AdjustHead $record): ?string => $record->creater?->name),
                    Placeholder::make('updated_at')
                        ->label(__('อัปเดตล่าสุด'))
                        ->inlineLabel()
                        ->content(fn(AdjustHead $record): ?string => $record->updated_at?->diffForHumans()),
                    Placeholder::make('updater.name')
                        ->label(__('โดย'))
                        ->inlineLabel()
                        ->content(fn(AdjustHead $record): ?string => $record->updater?->name)
                ])->compact()
            ])->columnSpan(['lg' => 1])->hidden(fn(?AdjustHead $record) => $record === null)
        ])->columns(4);
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
                ->label(__('ประเภทปรับยอด'))
                ->toggleable()
                ->searchable()
                ->sortable()
                ->badge()
                ->color(function(string $state): string {
                    return match($state) {
                        'การจัดซื้อ' => 'info',
                        'การปรับยอด' => 'warning'
                    };
                }),
            TextColumn::make('supplier.name')
                ->label(__('ชื่อร้าน'))
                ->toggleable()
                ->searchable()
                ->sortable(),
            TextColumn::make('purchase_at')
                ->label(__('วันที่สั่งซื้อ'))
                ->toggleable()
                ->sortable()
                ->date('j F Y'),
            TextColumn::make('creater.name')
                ->label(__('สร้างโดย'))
                ->toggleable()
                ->searchable()
                ->sortable(),
            TextColumn::make('created_at')
                ->label(__('วันที่สร้าง'))
                ->toggleable()
                ->sortable()
                ->date('j F Y'),
            TextColumn::make('updater.name')
                ->label(__('อัปเดตโดย'))
                ->toggleable(isToggledHiddenByDefault:true)
                ->searchable()
                ->sortable(),
            TextColumn::make('updated_at')
                ->label(__('วันที่อัปเดต'))
                ->toggleable(isToggledHiddenByDefault:true)
                ->sortable()
                ->date('j F Y')
        ])
        ->defaultSort('id', 'desc')
        ->filters([
            SelectFilter::make('category')
                ->label(__('ประเภทการปรับยอด'))
                ->relationship('category', 'name')
                ->native(false)
        ])
        ->actions([
            EditAction::make()->openUrlInNewTab(),
            DeleteAction::make()
        ], position: ActionsPosition::BeforeCells)
        ->bulkActions([DeleteBulkAction::make()]);
    }

    public static function getPages(): array {
        return [
            'index' => ListAdjusts::route('/'),
            'create' => CreateAdjust::route('/create'),
            'edit' => EditAdjust::route('/{record}/edit')
        ];
    }

    public static function getAdjustitemsRepeater(): Repeater {
        return TableRepeater::make('adjustitems')
            ->hiddenLabel()
            ->addActionLabel('เพิ่มอุปกรณ์/รุ่น')
            ->relationship()
            ->headers([
                Header::make('อุปกรณ์/รุ่น')->align(Alignment::Left)->width('250px'),
                Header::make('ผลต่อคลัง')->align(Alignment::Center)->width('80px'),
                Header::make('จำนวน')->align(Alignment::Center),
                Header::make('ราคาต่อหน่วย')->align(Alignment::Center),
                Header::make('ราคารวม')->align(Alignment::Center),
                Header::make('หมายเลขวิทยุสื่อสาร')->align(Alignment::Center)->width('150px')
            ])
            ->schema([
                Select::make('product_id')
                    ->placeholder(__('เลือกอุปกรณ์'))
                    ->relationship('product', 'name')
                    ->options(Product::query()->pluck('name', 'id'))
                    ->searchable()
                    ->preload()
                    ->reactive()
                    ->afterStateHydrated(function($state, Get $get, Set $set) {
                        self::pullPriceProduct($state, $set);
                        self::updateRowCost($get, $set);
                    })
                    ->afterStateUpdated(function($state, Get $get, Set $set) {
                        self::pullPriceProduct($state, $set);
                        self::updateRowCost($get, $set);
                    })
                    ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                    ->required(),
                Select::make('effective')
                    ->placeholder(__('+,-'))
                    ->options(['plus' => 'เพิ่ม', 'minus' => 'ลด'])
                    ->native(false)
                    ->required()
                    ->validationMessages([
                        'required' => 'โปรดเลือกผลต่อคลัง',
                    ]),
                TextInput::make('quantity')
                    ->numeric()
                    ->minValue(1)
                    ->maxValue(1000)
                    ->default(0)
                    ->live(onBlur: true)
                    ->afterStateUpdated(function(Get $get, Set $set) {
                        self::updateRowCost($get, $set);
                    })
                    ->rule(function(Get $get, ?Model $record = null) {
                        return function($attribute, $value, $fail) use($get, $record) {
                            $productId = $get('product_id');
                            $effective = $get('effective');
                            $store = Store::where('product_id', $productId)->first();
                            $newQuantity = $store->q_all ?? 0;
                            if ($record) {
                                $originalQuantity = $record->quantity;
                                $originalEffective = $record->effective;
                                if ($originalEffective === 'plus') {
                                    $newQuantity -= $originalQuantity;
                                } elseif($originalEffective === 'minus') {
                                    $newQuantity += $originalQuantity;
                                }
                            }
                            if($effective === 'plus') {
                                $newQuantity += $value;
                            } elseif($effective === 'minus') {
                                $newQuantity -= $value;
                            }
                            if($newQuantity < 0) {
                                $fail('ไม่สามารถบันทึกได้ คลังอุปกรณ์มีไม่เพียงพอต่อการปรับยอด');
                            }
                        };
                    })
                    ->required(),
                TextInput::make('unit_price')
                    ->disabled()
                    ->default(0)
                    ->dehydrated(),
                TextInput::make('qty_x_price')
                    ->disabled()
                    ->default(0)
                    ->dehydrated(),
                Select::make('serials')
                    ->relationship('serials', 'name')
                    ->options(function(Get $get) {
                        $productId = $get('product_id');
                        return Serial::where('product_id', $productId)->pluck('name', 'id');
                    })
                    ->searchable()
                    ->preload()
                    ->multiple()
                    ->reactive()
                    ->disabled(function(Get $get) {return $get('category_id') != 3;})
            ])
            ->extraItemActions([
                Action::make('openProduct')
                    ->tooltip('Open product')
                    ->icon('heroicon-m-arrow-top-right-on-square')
                    ->url(function(array $arguments, Repeater $component): ?string {
                        $itemData = $component->getRawItemState($arguments['item']);
                        $product = Product::find($itemData['product_id']);
                        if(!$product) { return null; }
                        return ProductResource::getUrl('edit', ['record' => $product]);
                    }, shouldOpenInNewTab: true)
                    ->hidden(fn(array $arguments, Repeater $component): bool =>
                        array_key_exists('item', $arguments) && blank($component->getRawItemState($arguments['item'])['product_id'])
                    )
            ])
            ->deleteAction( fn(Action $action) => $action->requiresConfirmation())
            ->orderColumn('sort')
            ->defaultItems(1)
            ->required()
        ;
    }

    public static function pullPriceProduct($state, Set $set) {
        $product = Product::find($state);
        $push_price_product = $product ? $product->price_product ?? 0 : 0;
        $price_product_format = intval($push_price_product) == $push_price_product ? intval($push_price_product) : $push_price_product;
        $set('unit_price', $price_product_format);
        $set('category_id', $product?->category_id);
    }

    public static function updateRowCost(Get $get, Set $set) {
        $price = $get('unit_price');
        $qty = $get('quantity');
        $qty_price = $price * $qty;
        $set('qty_x_price', $qty_price);
    }
}
