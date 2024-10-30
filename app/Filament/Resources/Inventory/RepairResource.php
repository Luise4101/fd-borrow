<?php

namespace App\Filament\Resources\Inventory;

use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use App\Models\Inventory\Store;
use App\Models\Inventory\Serial;
use Filament\Resources\Resource;
use App\Models\Inventory\Product;
use Illuminate\Http\UploadedFile;
use App\Models\Inventory\RepairHead;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Filters\SelectFilter;
use Filament\Forms\Components\Placeholder;
use Filament\Tables\Enums\ActionsPosition;
use Filament\Forms\Components\Actions\Action;
use Filament\Tables\Actions\DeleteBulkAction;
use App\Filament\Resources\Inventory\ProductResource;
use App\Filament\Resources\Inventory\RepairResource\Pages\EditRepair;
use App\Filament\Resources\Inventory\RepairResource\Pages\ListRepairs;
use App\Filament\Resources\Inventory\RepairResource\Pages\CreateRepair;

class RepairResource extends Resource
{
    protected static ?string $model = RepairHead::class;
    protected static ?string $modelLabel = 'การส่งซ่อมอุปกรณ์ ';
    protected static ?string $navigationIcon = 'heroicon-o-wrench';
    protected static ?string $navigationLabel = '3.5 การส่งซ่อมอุปกรณ์';
    protected static ?string $activeNavigationIcon = 'heroicon-s-wrench';

    public static function form(Form $form): Form
    {
        return $form ->schema([
            Group::make()->schema([
                Section::make()->schema([
                    Select::make('status_id')
                        ->hiddenLabel()
                        ->prefix(__('สถานะ'))
                        ->relationship('status','name')
                        ->native(false)
                        ->default(15)
                        ->required(),
                    Select::make('supplier_id')
                        ->hiddenLabel()
                        ->prefix(__('ร้าน/หน่วยงาน'))
                        ->relationship('supplier','name')
                        ->native(false),
                    DatePicker::make('sended_at')
                        ->hiddenLabel()
                        ->prefix(__('วันที่ส่งซ่อม'))
                        ->native(false)
                        ->displayFormat('j F Y'),
                    DatePicker::make('returned_at')
                        ->hiddenLabel()
                        ->prefix(__('วันที่รับคืน'))
                        ->native(false)
                        ->displayFormat('j F Y'),
                    TextInput::make('price_repair_all')
                        ->hiddenLabel()
                        ->prefix(__('ราคาซ่อมทั้งหมด'))
                        ->default(0)
                        ->readonly()
                        ->dehydrated()
                ])->columns(3),
                Section::make()->schema([ static::getRepairitemsRepeater() ])->compact(),
                Section::make()->schema([
                    FileUpload::make('attachment')
                        ->label(__('ไฟล์แนบ'))
                        ->directory('attachments/repair')
                        ->imageEditor()
                        ->imageEditorAspectRatios([null, '16:9', '4:3', '1:1'])
                        ->getUploadedFileNameForStorageUsing(fn(UploadedFile $file) => Date('YmdHis').'_Repair_'.Str::random(5).'.'.$file->getClientOriginalExtension())
                        ->openable(),
                    Textarea::make('note')
                        ->label(__('รายละเอียดเพิ่มเติม'))
                        ->placeholder(__('ข้อมูลเพิ่มเติมเกี่ยวกับการการส่งซ่อมอุปกรณ์'))
                        ->autosize()
                        ->maxLength(500)
                ])
            ])->columnSpan(['lg' => fn(?RepairHead $record) => $record === null ? 4 : 3]),
            Group::make()->schema([
                Section::make()->schema([
                    Placeholder::make('created_at')
                        ->label(__('สร้างเมื่อ'))
                        ->inlineLabel()
                        ->content(fn(RepairHead $record): ?string => $record->created_at?->diffForHumans()),
                    Placeholder::make('creater.name')
                        ->label(__('โดย'))
                        ->inlineLabel()
                        ->content(fn(RepairHead $record): ?string => $record->creater?->name),
                    Placeholder::make('updated_at')
                        ->label(__('อัปเดตล่าสุด'))
                        ->inlineLabel()
                        ->content(fn(RepairHead $record): ?string => $record->updated_at?->diffForHumans()),
                    Placeholder::make('updater.name')
                        ->label(__('โดย'))
                        ->inlineLabel()
                        ->content(fn(RepairHead $record): ?string => $record->updater?->name)
                ])->compact()
            ])->columnSpan(['lg' => 1])->hidden(fn(?RepairHead $record) => $record === null)
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
                TextColumn::make('supplier.name')
                    ->label(__('ชื่อร้าน'))
                    ->toggleable()
                    ->searchable()
                    ->sortable(),
                TextColumn::make('sended_at')
                    ->label(__('วันที่ส่งซ่อม'))
                    ->toggleable()
                    ->sortable()
                    ->date('j F Y'),
                TextColumn::make('returned_at')
                    ->label(__('วันที่รับคืน'))
                    ->toggleable()
                    ->sortable()
                    ->date('j F Y'),
                TextColumn::make('price_repair_all')
                    ->label(__('รวมค่าซ่อม'))
                    ->toggleable()
                    ->sortable(),
                TextColumn::make('status.name')
                    ->label(__('สถานะการส่งซ่อม'))
                    ->toggleable()
                    ->searchable()
                    ->sortable()
                    ->badge(),
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
                SelectFilter::make('status')
                    ->label(__('สถานะการส่งซ่อม'))
                    ->relationship('status', 'name')
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
            'index' => ListRepairs::route('/'),
            'create' => CreateRepair::route('/create'),
            'edit' => EditRepair::route('/{record}/edit')
        ];
    }

    public static function getRepairitemsRepeater(): Repeater {
        return Repeater::make('repairitems')
            ->hiddenLabel()
            ->addActionLabel('เพิ่มอุปกรณ์/รุ่น')
            ->relationship()
            ->schema([
                Group::make()->columns(5)->schema([
                    Select::make('product_id')
                        ->hiddenLabel()
                        ->placeholder(__('เลือกอุปกรณ์'))
                        ->options(Product::query()->pluck('name', 'id'))
                        ->searchable()
                        ->preload()
                        ->reactive()
                        ->afterStateUpdated(function($state, Set $set) {
                            $product = Product::find($state);
                            $set('category_id', $product?->category_id);
                        })
                        ->required(),
                    TextInput::make('quantity')
                        ->hiddenLabel()
                        ->prefix(__('จำนวน'))
                        ->numeric()
                        ->minValue(0)
                        ->maxValue(1000)
                        ->live(onBlur: true)
                        ->rules(function(Get $get, ?Model $record = null) {
                            return function($attribute, $value, $fail) use($get, $record) {
                                $productId = $get('product_id');
                                $store = Store::where('product_id', $productId)->first();
                                $qAll = $store->q_all;
                                $qWaste = $store->q_waste;
                                if ($record) {
                                    $qWaste = $qWaste - $record->quantity + $value;
                                } else {
                                    $qWaste += $value;
                                }
                                if ($qWaste > $qAll) {
                                    $fail('อุปกรณ์มีไม่พอสำหรับซ่อมแซม');
                                }
                            };
                        })
                        ->afterStateUpdated(function($state, Get $get, Set $set) {
                            if($state < 0) {$set('quantity', abs($state));}
                            self::updateRowCost($get, $set);
                        })
                        ->required(),
                    TextInput::make('unit_price')
                        ->hiddenLabel()
                        ->suffix(__('บาท'))
                        ->placeholder(__('ราคาซ่อมต่อหน่วย'))
                        ->numeric()
                        ->minValue(0)
                        ->live(onBlur: true)
                        ->afterStateUpdated(function($state, Get $get, Set $set) {
                            if($state < 0) {$set('unit_price', abs($state));}
                            self::updateRowCost($get, $set);
                        })
                        ->dehydrated(),
                    TextInput::make('price_repair')
                        ->hiddenLabel()
                        ->prefix(__('รวมค่าซ่อม'))
                        ->afterStateHydrated(function($state, Get $get, Set $set) {
                            $price_repair = floatval($state) ?? 0;
                            $qty = floatval($get('quantity')) ?? 0;
                            if ($qty > 0) {
                                $unit_price = $price_repair / $qty;
                            } else {
                                $unit_price = 0;
                            }
                            $set('unit_price', $unit_price);
                        })
                        ->readonly()
                        ->dehydrated(),
                    Select::make('status')
                        ->hiddenLabel()
                        ->placeholder(__('เลือกสถานะ'))
                        ->relationship('status','name')
                        ->native(false)
                        ->default(15)
                        ->required()
                ]),
                Group::make()->columns(2)->schema([
                    Select::make('serials')
                        ->hiddenLabel()
                        ->placeholder(__('หมายเลขวิทยุสื่อสาร'))
                        ->relationship('serials', 'name')
                        ->options(function(Get $get) {
                            $productId = $get('product_id');
                            return Serial::where('product_id', $productId)->pluck('name', 'id');
                        })
                        ->searchable()
                        ->preload()
                        ->multiple()
                        ->reactive()
                        ->disabled(function(Get $get) {return $get('category_id') != 3;}),
                    Textarea::make('note')
                        ->hiddenLabel()
                        ->placeholder(__('รายละเอียดการซ่อม/ความเสียหาย'))
                        ->maxLength(500)
                        ->autosize()
                ])
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
            ->collapsible()
            ->cloneable()
            ->required()
            ->live(onBlur: true)
            ->afterStateHydrated(function(Get $get, Set $set) {
                self::updateTotalCost($get, $set);
            })
			->afterStateUpdated(function (Get $get, Set $set) {
                self::updateTotalCost($get, $set);
            })
        ;
    }

    public static function updateRowCost(Get $get, Set $set) {
        $price = floatval($get('unit_price')) ?? 0;
        $qty = floatval($get('quantity')) ?? 0;
        $price_repair = $price * $qty;
        $set('price_repair', $price_repair);
    }

    public static function updateTotalCost(Get $get, Set $set) {
        $items = collect($get('repairitems'))->filter(fn($item) => !empty($item['price_repair']));
        $priceRepairAll = 0;
        foreach($items as $item) {
            $price_repair = floatval($item['price_repair'] ?? 0);
            $priceRepairAll += $price_repair;
        }
        $set('price_repair_all', number_format($priceRepairAll, 2, '.', ''));
    }
}
