<?php

namespace App\Filament\Resources\Main;

use App\Models\User;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Form;
use App\Models\Asset\Kong;
use Filament\Tables\Table;
use App\Enums\BorrowStatus;
use Filament\Support\RawJs;
use Illuminate\Support\Str;
use App\Models\Asset\Samnak;
use App\Models\Asset\Section;
use Filament\Facades\Filament;
use App\Models\Inventory\Store;
use App\Models\Main\BorrowHead;
use App\Models\Inventory\Serial;
use Filament\Resources\Resource;
use App\Models\Inventory\Product;
use Awcodes\TableRepeater\Header;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Facades\Log;
use Filament\Forms\Components\Group;
use Illuminate\Support\Facades\Http;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Support\Enums\Alignment;
use App\Http\Controllers\HRController;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\ImageColumn;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Filters\SelectFilter;
use Filament\Forms\Components\Placeholder;
use Filament\Tables\Enums\ActionsPosition;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\DateTimePicker;
use Filament\Tables\Actions\DeleteBulkAction;
use Awcodes\TableRepeater\Components\TableRepeater;
use App\Filament\Resources\Inventory\ProductResource;
use Filament\Forms\Components\Section as FilamentSection;
use App\Filament\Resources\Main\BorrowManageResource\Pages\EditBorrowManage;
use App\Filament\Resources\Main\BorrowManageResource\Pages\ListBorrowManages;
use App\Filament\Resources\Main\BorrowManageResource\Pages\CreateBorrowManage;

class BorrowManageResource extends Resource
{
    protected static ?string $model = BorrowHead::class;
    protected static ?string $modelLabel = 'จัดการคำขอใช้วิทยุสื่อสาร ';
    protected static ?string $navigationIcon = 'heroicon-o-folder-open';
    protected static ?string $navigationLabel = '4.2 จัดการคำขอใช้วิทยุสื่อสาร';
    protected static ?string $activeNavigationIcon = 'heroicon-s-folder-open';

    public static function form(Form $form): Form {
        return $form->schema([
            static::getBorrowheadFormSchema(),
            Group::make()->schema([
                static::getBorrowitemsRepeater(),
                static::getBorrowinfosRepeater(),
                FilamentSection::make()->schema([Textarea::make('note')->label(__('รายละเอียดอื่น ๆ'))->maxLength(500)->autosize()])
            ])->columnSpan(['sm' => 4, 'md' => 4, 'lg' => 3]),
            Group::make()->schema([
                FilamentSection::make()->schema([
                    Placeholder::make('created_at')
                        ->label('สร้างใบงาน')
                        ->inlineLabel()
                        ->content(fn(BorrowHead $record): ?string => $record->created_at?->diffForHumans()),
                    Placeholder::make('updated_at')
                        ->label('อัปเดตล่าสุด')
                        ->inlineLabel()
                        ->content(fn(BorrowHead $record): ?string => $record->updated_at?->diffForHumans()),
                    Placeholder::make('updated_by')
                        ->label('อัปเดตโดย')
                        ->inlineLabel()
                        ->content(fn(BorrowHead $record): ?string => User::find($record->updated_by)?->name)
                        ->hidden(fn(BorrowHead $record): bool => $record->updated_by === null)
                ])->hidden(fn(?BorrowHead $record) => $record === null)->compact(),
                static::getBorrowasideFormSchema()
            ])->columnSpan(['sm' => 4, 'md' => 4, 'lg' => 1])
        ])->columns(4);
    }

    public static function table(Table $table): Table {
        return $table->columns([
            TextColumn::make('id')
                ->label(__('ID'))
                ->toggleable()
                ->searchable()
                ->sortable(),
            TextColumn::make('borrower.name')
                ->label(__('ผู้ยืม'))
                ->toggleable()
                ->searchable()
                ->sortable(),
            TextColumn::make('status.name')
                ->label(__('สถานะการยืม'))
                ->badge()
                ->color(function(string $state): string {
                    return match($state) {
                        'ขอใหม่' => 'info',
                        'หน.กองอนุมัติ' => 'warning',
                        'หน.กองไม่อนุมัติ' => 'danger',
                        'ส่งมอบของ' => 'warning',
                        'รับของคืน' => 'success',
                        'จบงาน' => 'success',
                        'ยกเลิก' => 'danger'
                    };
                })
                ->icon(function(string $state): string {
                    return match($state) {
                        'ขอใหม่' => 'heroicon-m-sparkles',
                        'หน.กองอนุมัติ' => 'heroicon-m-check-badge',
                        'หน.กองไม่อนุมัติ' => 'heroicon-m-x-circle',
                        'ส่งมอบของ' => 'heroicon-m-truck',
                        'รับของคืน' => 'heroicon-m-arrow-path',
                        'จบงาน' => 'heroicon-m-shield-check',
                        'ยกเลิก' => 'heroicon-m-x-circle'
                    };
                })
                ->toggleable()
                ->searchable()
                ->sortable(),
            TextColumn::make('pickup_at')
                ->label(__('วันที่รับของ'))
                ->toggleable()
                ->searchable()
                ->sortable()
                ->date('j M Y'),
            TextColumn::make('return_schedule')
                ->label(__('กำหนดคืน'))
                ->toggleable()
                ->searchable()
                ->sortable()
                ->date('j M Y'),
            TextColumn::make('return_at')
                ->label(__('วันที่คืนล่าสุด'))
                ->toggleable()
                ->searchable()
                ->sortable()
                ->date('j M Y, H:i'),
            Textcolumn::make('price_borrow_all')
                ->label(__('ค่ามัดจำ'))
                ->numeric(decimalPlaces:0,decimalSeparator:'.',thousandsSeparator:',')
                ->toggleable()
                ->sortable(),
            TextColumn::make('price_fine')
                ->label(__('ค่าปรับ'))
                ->numeric(decimalPlaces:0,decimalSeparator:'.',thousandsSeparator:',')
                ->toggleable()
                ->sortable(),
            TextColumn::make('activity_name')
                ->label(__('ชื่อกิจกรรม'))
                ->limit(30)
                ->toggleable(isToggledHiddenByDefault:true)
                ->searchable()
                ->sortable(),
            TextColumn::make('activity_place')
                ->label(__('สถานที่ใช้งาน'))
                ->limit(30)
                ->toggleable(isToggledHiddenByDefault:true)
                ->searchable()
                ->sortable(),
            ImageColumn::make('attachment')
                ->label(__('ไฟล์แนบ'))
                ->stacked()
                ->toggleable(isToggledHiddenByDefault:true),
            ImageColumn::make('proof_payment')
                ->label(__('หลักฐานการร่วมบุญ'))
                ->stacked()
                ->toggleable(isToggledHiddenByDefault:true),
            TextColumn::make('note')
                ->label(__('อื่นๆ'))
                ->limit(30)
                ->toggleable(isToggledHiddenByDefault:true),
            TextColumn::make('created_at')
                ->label(__('วันที่สร้าง'))
                ->toggleable(isToggledHiddenByDefault:true)
                ->searchable()
                ->sortable()
                ->date('j F Y, H:i'),
            TextColumn::make('updated_at')
                ->label(__('วันที่อัปเดต'))
                ->toggleable(isToggledHiddenByDefault:true)
                ->searchable()
                ->sortable()
                ->date('j F Y, H:i')
        ])->filters([
            SelectFilter::make('status')
                ->label(__('สถานะการยืม'))
                ->relationship('status','name')
                ->native(false)
        ])->actions([
            EditAction::make()->openUrlInNewTab(),
            DeleteAction::make()
        ], position:ActionsPosition::BeforeCells)->defaultSort('id', 'desc')
        ->bulkActions([DeleteBulkAction::make()]);
    }

    public static function getPages(): array {
        return [
            'index' => ListBorrowManages::route('/'),
            'create' => CreateBorrowManage::route('/create'),
            'edit' => EditBorrowManage::route('/{record}/edit')
        ];
    }

    public static function getBorrowheadFormSchema() {
        return FilamentSection::make(new HtmlString('<style>.bg-color{background-color:#d9edf6;}</style>'))->schema([
            TextInput::make('id')
                ->hiddenLabel()
                ->prefix(__('BID'))
                ->readonly()
                ->columnSpan(1),
            ToggleButtons::make('status_id')
                ->hiddenLabel()
                ->inline()
                ->options(BorrowStatus::class)
                ->required()
                ->columnSpan(5),
            Placeholder::make('data_borrower')->hiddenLabel()
                ->content(function($record) {
                    $borrower = User::where('id', $record->borrower_id)->first();
                    $head = (new HRController())->fetchApproverData($record->qhead);
                    return new HtmlString('
                        <style>
                            .gap-6 {gap: 0.5rem; !important}
                            .fi-fo-repeater-item-header {padding-bottom: 0.5rem !important;padding-top: 0.5rem !important;}
                            td.value {padding-left: 0.5rem;padding-right: 1rem;}
                            .label {font-weight: 700;}
                            .value {font-size: 0.95rem}
                            .bg-lime-100 {background-color: #ecfccb;}
                        </style>
                        <table style="max-width:100%;">
                            <tr>
                                <td class="label">ผู้ยืม:</td>
                                <td class="value">'.$borrower->fullname.'</td>
                                <td class="label">เบอร์โทร:</td>
                                <td class="value">'.$record->borrower_tel.'</td>
                                <td class="label">Line ID:</td>
                                <td class="value">'.$record->borrower_lineid.'</td>
                                <td class="label">Email:</td>
                                <td class="value">'.$borrower->email.'</td>
                            </tr>
                            <tr>
                                <td class="label">หัวหน้ากอง:</td>
                                <td class="value">'.$record->chead.'</td>
                                <td class="label">เบอร์โทร:</td>
                                <td class="value">'.$head->mobile.'</td>
                                <td class="label">Line ID:</td>
                                <td class="value">'.$head->lineid.'</td>
                                <td class="label">Email:</td>
                                <td class="value">'.$head->email.'</td>
                            </tr>
                        </table>
                    ');
                })->columnSpanFull(),
            DateTimePicker::make('pickup_at')
                ->hiddenLabel()
                ->prefix('วันที่รับอุปกรณ์')
                ->seconds(false)
                ->native(false)
                ->firstDayOfWeek(7)
                ->displayFormat('j F Y H:i')
                ->default(now())
                ->required()
                ->columnSpan(2),
            DateTimePicker::make('return_schedule')
                ->hiddenLabel()
                ->prefix('กำหนดส่งคืน')
                ->seconds(false)
                ->native(false)
                ->firstDayOfWeek(7)
                ->minutesStep(15)
                ->displayFormat('j F Y H:i')
                ->default(now())
                ->columnSpan(2),
            DateTimePicker::make('return_at')
                ->hiddenLabel()
                ->prefix('วันที่คืนอุปกรณ์')
                ->visibleOn('edit')
                ->seconds(false)
                ->native(false)
                ->firstDayOfWeek(7)
                ->displayFormat('j F Y H:i')
                ->disabled()
                ->columnSpan(2),
            Placeholder::make('data_activity')->hiddenLabel()->content(function($record) {
                $approveDate = $record?->approved_at ? $record->approved_at->translatedFormat('j F Y H:i') : 'N/A';
                return new HtmlString('
                    <table><tr>
                        <td class="label">วันที่อนุมัติ :</td>
                        <td class="value">'.$approveDate.'</td>
                        <td class="label">ชื่อกิจกรรม :</td>
                        <td class="value">'.$record->activity_name.'</td>
                        <td class="label">สถานที่ใช้งาน :</td>
                        <td class="value">'.$record->activity_place.'</td>
                    </tr></table>
                ');
            })->columnSpanFull()
        ])->extraAttributes(['class' => 'bg-color'])->columns(6)->columnSpan(4)->compact();
    }

    public static function getBorrowitemsRepeater() {
        return FilamentSection::make('รายละเอียดอุปกรณ์ที่ยืม')->schema([
            TableRepeater::make('borrowitems')
                ->relationship()
                ->hiddenLabel()
                ->headers([
                    Header::make('รายละเอียดอุปกรณ์')->width('210px'),
                    Header::make('จำนวนขอ')->align(Alignment::Center)->width('80px'),
                    Header::make('จำนวนให้ยืม')->align(Alignment::Center)->width('80px'),
                    Header::make('จำนวนคืน')->align(Alignment::Center)->width('80px'),
                    Header::make('เลขทะเบียนวิทยุสื่อสาร')->align(Alignment::Center)
                ])
                ->schema([
                    Select::make('product_id')
                        ->hiddenLabel()
                        ->placeholder(__('เลือกอุปกรณ์'))
                        ->options(Product::query()->pluck('name','id'))
                        ->searchable()
                        ->preload()
                        ->reactive()
                        ->afterStateUpdated(function ($state, Set $set) {
                            $product = Product::find($state);
                            $price_borrow = $product ? $product->price_borrow ?? 0 : 0;
                            $price_product = $product ? $product->price_product ?? 0 : 0;
                            $price_borrow = intval($price_borrow) == $price_borrow ? intval($price_borrow) : $price_borrow;
                            $price_product = intval($price_product) == $price_product ? intval($price_product) : $price_product;
                            $set('price_borrow', $price_borrow);
                            $set('price_product', $price_product);
                            $set('category_id', $product?->category_id);
                        })
                        ->afterStateHydrated(function ($state, Set $set) {
                            if($product = Product::find($state)) {
                                $price_borrow = $product->price_borrow ?? 0;
                                $price_product = $product->price_product ?? 0;
                                $price_borrow = intval($price_borrow) == $price_borrow ? intval($price_borrow) : $price_borrow;
                                $price_product = intval($price_product) == $price_product ? intval($price_product) : $price_product;
                                $set('price_borrow', $price_borrow);
                                $set('price_product', $price_product);
                                $set('category_id', $product?->category_id);
                            }
                        })
                        ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                        ->required()
                        ->columnSpan(2),
                    Hidden::make('price_product')->dehydrated(),
                    Hidden::make('price_borrow')->dehydrated(),
                    TextInput::make('q_request')
                        ->hiddenLabel()
                        ->numeric()
                        ->minValue(0)
                        ->maxValue(1000)
                        ->columnSpan(1),
                    TextInput::make('q_lend')
                        ->hiddenLabel()
                        ->numeric()
                        ->live(onBlur: true)
                        ->rules(function(Get $get, ?Model $record = null) {
                            return function($attribute, $value, $fail) use($get, $record) {
                                $productId = $get('product_id');
                                $store = Store::where('product_id', $productId)->first();
                                $qAll = $store->q_all - $store->q_waste;
                                $qLend = $store->q_borrow;
                                if($record) {
                                    $qLend = $qLend - $record->q_lend + $value;
                                } else {
                                    $qLend += $value;
                                }
                                if($qLend > $qAll) {$fail('อุปกรณ์มีไม่พอสำหรับการยืม');}
                            };
                        })
                        ->dehydrated()
                        ->required()
                        ->columnSpan(1),
                    TextInput::make('q_all_return')
                        ->hiddenLabel()
                        ->numeric()
                        ->default(0)
                        ->columnSpan(1),
                    Hidden::make('total_price_borrow')
                        ->hiddenLabel()
                        ->dehydrated(),
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
                        ->dehydrated()
                        ->disabled(function(Get $get) {return $get('category_id') != 3;})
                        ->columnSpanFull()
                ])->deleteAction(fn(Action $action) => $action->requiresConfirmation())
                ->extraItemActions([
                    Action::make('openProduct')
                        ->tooltip('Open product')
                        ->icon('heroicon-m-arrow-top-right-on-square')
                        ->url(function (array $arguments, Repeater $component): ?string {
                            $itemData = $component->getRawItemState($arguments['item']);
                            $product = Product::find($itemData['product_id']);
                            if(!$product) {return null;}
                            return ProductResource::getUrl('edit', ['record' => $product]);
                        }, shouldOpenInNewTab: true)
                        ->hidden(fn(array $arguments, Repeater $component): bool =>
                            array_key_exists('item', $arguments) && blank($component->getRawItemState($arguments['item'])['product_id'])
                        )
                ])->emptyLabel('กรุณาเพิ่มอุปกรณ์ที่จะขอยืม')
                ->addActionLabel('เพิ่มอุปกรณ์ที่ยืม')
                ->orderColumn('sort')
                ->defaultItems(1)
                ->streamlined()
                ->collapsible()
                ->required()
                ->live(onBlur: true)
                ->afterStateUpdated(function (Get $get, Set $set) {
                    self::updateTotalCost($get, $set);
                })
                ->afterStateHydrated(function (Get $get, Set $set) {
                    self::updateTotalCost($get, $set);
                })
        ])->compact()->collapsible();
    }

    public static function getBorrowinfosRepeater() {
        return FilamentSection::make('ข้อมูลวัตถุประสงค์การใช้งาน')->schema([
            TextInput::make('q_attendee')
                ->prefix('จำนวนผู้ร่วมงาน')
                ->placeholder(0)
                ->suffix('ท่าน')
                ->hiddenLabel()
                ->numeric(),
            TextInput::make('q_staff')
                ->prefix('จำนวนเจ้าหน้าที่')
                ->placeholder(0)
                ->suffix('ท่าน')
                ->hiddenLabel()
                ->numeric(),
            TextInput::make('q_all_use')
                ->prefix('รวมจำนวนวิทยุสื่อสาร')
                ->hiddenLabel()
                ->suffix('ตัว')
                ->default(0)
                ->readonly(),
            TableRepeater::make('borrowinfos')
                ->relationship()
                ->hiddenLabel()
                ->headers([
                    Header::make('ฝ่ายงานวัตถุประสงค์การใช้งาน')->align(Alignment::Center)->width('560px'),
                    Header::make('จำนวนวิทยุสื่อสารที่ใช้')->align(Alignment::Center)
                ])->schema([
                    TextInput::make('purpose')
                        ->maxLength(200)
                        ->hiddenLabel(),
                    TextInput::make('q_use')
                        ->placeholder(0)
                        ->hiddenLabel()
                        ->numeric()
                ])->emptyLabel('กรุณากรอกข้อมูลเพื่อใช้ในการพิจารณาจำนวนที่ยืม')
                ->addActionLabel('เพิ่มรายละเอียด')
                ->orderColumn('sort')
                ->live(onBlur: true)
                ->columnSpanFull()
                ->streamlined()
                ->cloneable()
                ->afterStateUpdated(function(Get $get, Set $set) {
                    self::updateQuantityUse($get, $set);
                })
                ->afterStateHydrated(function(Get $get, Set $set) {
                    self::updateQuantityUse($get, $set);
                })
        ])->extraAttributes(['class' => 'bg-lime-100'])->columns(3)->compact()->collapsible();
    }

    public static function getBorrowasideFormSchema() {
        return FilamentSection::make()->schema([
            TextInput::make('price_borrow_all')
                ->hiddenLabel()
                ->prefix('รวมค่ามัดจำ')
                ->numeric()
                ->readOnly(),
            TextInput::make('price_fine')
                ->hiddenLabel()
                ->prefix('รวมค่าปรับ')
                ->numeric()
                ->readOnly(),
            FileUpload::make('attachment')
                ->label('ไฟล์กำหนดการ หรืออื่นๆ')
                ->directory('attachments/borrow')
                ->multiple()
                ->imageEditor()
                ->imageEditorAspectRatios([null,'16:9','4:3','1:1'])
                ->getUploadedFileNameForStorageUsing(fn(UploadedFile $file) => Date('YmdHis').'_Borrow_'.Str::random(8).'.'.$file->getClientOriginalExtension())
                ->openable()
                ->previewable()
                ->imagePreviewHeight('100')
                ->panelLayout('compact')
                ->reorderable()
                ->appendFiles(),
            FileUpload::make('proof_payment')
                ->label('หลักฐานการร่วมบุญ')
                ->directory('attachments/borrow')
                ->multiple()
                ->imageEditor()
                ->imageEditorAspectRatios([null,'16:9','4:3','1:1'])
                ->getUploadedFileNameForStorageUsing(fn(UploadedFile $file) => Date('YmdHis').'_Payment_'.Str::random(8).'.'.$file->getClientOriginalExtension())
                ->openable()
                ->previewable()
                ->imagePreviewHeight('100')
                ->panelLayout('compact')
                ->reorderable()
                ->appendFiles()
        ])->compact();
    }

    public static function updateTotalCost(Get $get, Set $set) {
        $items = collect($get('borrowitems'))->filter(fn($item) => !empty($item['price_borrow']));
        $priceBorrowAll = 0;
        foreach ($items as &$item) {
            $price_borrow = floatval($item['price_borrow'] ?? 0);
            $q_lend = floatval($item['q_lend'] ?? 0);
            $item['total_price_borrow'] = $price_borrow * $q_lend;
            $priceBorrowAll += $item['total_price_borrow'];
        }
        $set('price_borrow_all', number_format($priceBorrowAll, 2, '.', ''));
    }

    public static function updateQuantityUse(Get $get, Set $set) {
        $infos = collect($get('borrowinfos'))->filter(fn($info) => !empty($info['q_use']));
        $quantityUseAll = 0;
        foreach($infos as $info) {
            $quantityUseAll += $info['q_use'];
        }
        $set('q_all_use', number_format($quantityUseAll));
    }
}
