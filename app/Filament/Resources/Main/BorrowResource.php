<?php

namespace App\Filament\Resources\Main;

use Closure;
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
use Filament\Forms\Components\Split;
use Illuminate\Support\Facades\Http;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Support\Enums\Alignment;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
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
use App\Filament\Resources\Main\BorrowResource\Pages\EditBorrow;
use App\Filament\Resources\Main\BorrowResource\Pages\ListBorrows;
use App\Filament\Resources\Main\BorrowResource\Pages\CreateBorrow;

class BorrowResource extends Resource
{
    protected static ?string $model = BorrowHead::class;
    protected static ?string $modelLabel = 'รายการขอใช้วิทยุสื่อสาร ';
    protected static ?string $navigationIcon = 'heroicon-o-bookmark';
    protected static ?string $navigationLabel = '4.1 รายการขอใช้วิทยุสื่อสาร';
    protected static ?string $activeNavigationIcon = 'heroicon-s-bookmark';

    public static function form(Form $form): Form {
        return $form->schema([
            static::getBorrowheadFormSchema(),
            Group::make()->schema([
                static::getBorrowdetailSchema(),
                static::getBorrowitemsRepeater(),
                static::getBorrowinfosRepeater(),
                FilamentSection::make()->schema([Textarea::make('note')->label(__('รายละเอียดอื่น ๆ'))->maxLength(500)->autosize()])
            ])->columnSpan(['md' => 4, 'lg' => 3]),
            Group::make()->schema([
                FilamentSection::make()->schema([
                    Placeholder::make('created_at')
                        ->label('สร้างใบงาน')
                        ->inlineLabel()
                        ->content(fn (BorrowHead $record): ?string => $record->created_at?->diffForHumans()),
                    Placeholder::make('updated_at')
                        ->label('อัปเดตล่าสุด')
                        ->inlineLabel()
                        ->content(fn (BorrowHead $record): ?string => $record->updated_at?->diffForHumans())
                ])->hidden(fn (?BorrowHead $record) => $record ===null)->compact(),
                static::getBorrowasideFormSchema()
            ])->columnSpan(['md' => 4, 'lg' => 1])
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
            ViewAction::make(),
            EditAction::make()->openUrlInNewTab(),
            DeleteAction::make()
        ], position:ActionsPosition::BeforeCells)->defaultSort('id', 'desc')
        ->bulkActions([DeleteBulkAction::make()]);
    }

    public static function getPages(): array {
        return [
            'index' => ListBorrows::route('/'),
            'create' => CreateBorrow::route('/create'),
            'edit' => EditBorrow::route('/{record}/edit')
        ];
    }

    public static function getBorrowheadFormSchema() {
        $token= session('hrapi_token');
        $userId = Filament::auth()->id();
        $ADUser = User::findOrFail($userId);
        $api1 = 'https://api.dhammakaya.network/api/Person/getPersonAdInternal';
        $api2 = 'https://api.dhammakaya.network/api/Organization/GetApproveStepByLogin';
        $responsePersonData = Http::withOptions(['verify' => false])->withToken($token)->get($api1, ['aduser' => $ADUser['name']]);
        if($responsePersonData->successful()) {
            $dataResponse = $responsePersonData->json();
            $dataData = $dataResponse['Data'];
            $dataUser = $dataData[0];
        }
        $user_tel = $dataUser['Mobile'];
        $user_lineid = $dataUser['Lineid'];
        $user_qsamnak = $dataUser['DepartmentId'];
        $user_csamnak = $dataUser['Department'];
        $user_qsection = $dataUser['SectionId'];
        $user_csection = $dataUser['Section'];
        $user_qkong = $dataUser['OfficeId'];
        $user_ckong = $dataUser['Office'];
        $responseApproveData = Http::withOptions(['verify' => false])->withToken($token)->get($api2, ['aduser' => $ADUser['name']]);
        if($responseApproveData->successful()) {
            $dataResponse = $responseApproveData->json();
            $dataData = $dataResponse['Data'];
            $dataApprove = $dataData[0];
        }
        $approve_qhead = $dataApprove['HeadLogin'];
        $approve_chead = $dataApprove['HeadFullName'];
        $approve_mail = $dataApprove['HeadEmail'];
        return FilamentSection::make()->schema([
            TextInput::make('id')
                ->hiddenLabel()
                ->prefix(__('BID'))
                ->disabled()
                ->visibleOn('edit')
                ->columnSpan(1),
            ToggleButtons::make('status_id')
                ->hiddenLabel()
                ->inline()
                ->options(BorrowStatus::class)
                ->default(8)
                ->disabled()
                ->required()
                ->columnSpan(5),
            TextInput::make('activity_name')
                ->hiddenLabel()
                ->prefix('ชื่อกิจกรรม')
                ->maxLength(200)
                ->required()
                ->columnSpan(3),
            TextInput::make('activity_place')
                ->hiddenLabel()
                ->prefix('สถานที่ใช้งาน')
                ->maxLength(200)
                ->required()
                ->columnSpan(3),
            Select::make('borrower_id')
                ->hiddenLabel()
                ->prefix(__('ผู้ยืม'))
                ->disabled()
                ->relationship('borrower', 'fullname')
                ->native(false)
                ->default(Filament::auth()->id())
                ->columnSpan(2),
            TextInput::make('borrower_tel')
                ->hiddenLabel()
                ->prefix(__('เบอร์โทร'))
                ->maxLength(40)
                ->placeholder(__('000-000-0000'))
                ->mask(RawJs::make(<<<'JS'
                    $input.split(',').map(tel => {
                        tel = tel.trim();
                        return tel.startsWith('02') ? tel.replace(/(\d{2})(\d{3})(\d{4})/, '$1-$2-$3') : tel.replace(/(\d{3})(\d{3})(\d{4})/, '$1-$2-$3');
                    }).join(', ');
                JS))
                ->default($user_tel)
                ->dehydrateStateUsing(function($state) {
                    return collect(explode(',', $state))->map(function($tel) {
                        return preg_replace('/[^\d]/', '', $tel);
                    })->implode(', ');
                })
                ->columnSpan(2),
            TextInput::make('borrower_lineid')
                ->hiddenLabel()
                ->prefix(__('Line Id'))
                ->maxLength(50)
                ->default($user_lineid)
                ->columnSpan(2),
            Hidden::make('qsamnak')->default($user_qsamnak),
            TextInput::make('csamnak')
                ->hiddenLabel()
                ->prefix(__('สำนัก'))
                ->readonly()
                ->default($user_csamnak)
                ->afterStateHydrated(function($state, Get $get, Set $set) {
                    if(!$state) {
                        if($samnak = Samnak::where('qsamnak', $get('qsamnak'))->first()) {
                            $set('csamnak', $samnak->csamnak);
                        }
                    } else {
                        if($samnak = Samnak::where('qsamnak', $get('qsamnak'))->first()) {
                            if($samnak->csamnak !== $state) {
                                $samnak->update(['csamnak' => $state]);
                            }
                        } else {
                            $samnak = Samnak::create([
                                'qsamnak' => $get('qsamnak'),
                                'csamnak' => $state
                            ]);
                        }
                        $set('samnak_id', $samnak->id);
                    }
                })
                ->columnSpan(2),
            Hidden::make('qsection')->default($user_qsection),
            TextInput::make('csection')
                ->hiddenLabel()
                ->prefix(__('ฝ่าย'))
                ->readonly()
                ->default($user_csection)
                ->afterStateHydrated(function($state, Get $get, Set $set) {
                    if(!$state) {
                        if($section = Section::where('qsection', $get('qsection'))->first()) {
                            $set('csection', $section->csection);
                        }
                    } else {
                        if($section = Section::where('qsection', $get('qsection'))->first()) {
                            if($section->csection !== $state) {
                                $section->update(['csection' => $state]);
                            }
                        } else {
                            $section = Section::create([
                                'samnak_id' => $get('samnak_id'),
                                'qsection' => $get('qsection'),
                                'csection' => $state
                            ]);
                        }
                        $set('section_id', $section->id);
                    }
                })
                ->columnSpan(2),
            Hidden::make('qkong')->default($user_qkong),
            TextInput::make('ckong')
                ->hiddenLabel()
                ->prefix(__('กอง'))
                ->readonly()
                ->default($user_ckong)
                ->afterStateHydrated(function($state, Get $get, Set $set) {
                    if(!$state) {
                        if($kong = Kong::where('qkong', $get('qkong'))->first()) {
                            $set('ckong', $kong->ckong);
                        }
                    } else {
                        if($kong = Kong::where('qkong', $get('qkong'))->first()) {
                            if($kong->ckong !== $state) {
                                $kong->update(['ckong' => $state]);
                            }
                        } else {
                            $kong = Kong::create([
                                'samnak_id' => $get('samnak_id'),
                                'section_id' => $get('section_id'),
                                'qkong' => $get('qkong'),
                                'ckong' => $state
                            ]);
                        }
                    }
                })
                ->columnSpan(2),
            Hidden::make('qhead')->default($approve_qhead),
            TextInput::make('chead')
                ->hiddenLabel()
                ->prefix(__('หัวหน้ากอง'))
                ->maxLength(100)
                ->readonly()
                ->default($approve_chead)
                ->columnSpan(2),
            TextInput::make('head_mail')
                ->hiddenLabel()
                ->prefix(__('Email หัวหน้ากอง'))
                ->maxLength(100)
                ->readonly()
                ->default($approve_mail)
                ->afterStateHydrated(function(Get $get, Set $set) {
                    $borrower = User::findOrFail($get('borrower_id'));
                    $responseApi = Http::withOptions(['verify'=>false])->withToken(session('hrapi_token'))->get('https://api.dhammakaya.network/api/Organization/GetApproveStepByLogin', ['aduser'=>$borrower['name']]);
                    if($responseApi->successful()) {
                        $dataResponse = $responseApi->json();
                        $dataData = $dataResponse['Data'];
                        $dataApprove = $dataData[0];
                    }
                    $set('head_mail', $dataApprove['HeadEmail']);
                })
                ->columnSpan(2),
            DateTimePicker::make('approved_at')
                ->hiddenLabel()
                ->prefix('วันที่อนุมัติ')
                ->seconds(false)
                ->native(false)
                ->firstDayOfWeek(7)
                ->minutesStep(15)
                ->displayFormat('j F Y H:i น.')
                ->disabled()
                ->columnSpan(2),
            DateTimePicker::make('pickup_at')
                ->hiddenLabel()
                ->prefix('วันที่รับอุปกรณ์')
                ->seconds(false)
                ->native(false)
                ->firstDayOfWeek(7)
                ->minutesStep(15)
                ->displayFormat('j F Y H:i น.')
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
                ->displayFormat('j F Y H:i น.')
                ->placeholder(now()->format('j F Y, H:i'))
                ->required()
                ->columnSpan(2),
            DateTimePicker::make('return_at')
                ->hiddenLabel()
                ->prefix('วันที่คืนอุปกรณ์')
                ->visibleOn('edit')
                ->seconds(false)
                ->native(false)
                ->firstDayOfWeek(7)
                ->minutesStep(15)
                ->displayFormat('j F Y H:i น.')
                ->disabled()
                ->columnSpan(2),
        ])->columns(6)->columnSpan(4)->compact();
    }

    public static function getBorrowdetailSchema() {
        return FilamentSection::make(new HtmlString('ราคาอุปกรณ์ <span style="color:red;font-size:0.9rem;">(กรณีทำชำรุดหรือสูญหาย)</span>'))->schema([
            Placeholder::make('price_products')
                ->hiddenLabel()
                ->content(new HtmlString('
                    <style>
                        .pr-4 {padding-right: 1.5rem;}
                        .text-red-500 {color: red;}
                        .bg-lime-100 {background-color: #ecfccb;}
                        .grid-col-2 {grid-template-columns: repeat(2, minmax(0, 1fr));}
                    </style>
                    <div class="grid grid-col-2 gap-3">
                        <table>
                            <tr><td>1.</td><td>วิทยุสื่อสาร</td><td class="text-right text-red-500 font-semibold pr-4">2,500</td><td class="text-center">บาท</td></tr>
                            <tr><td>2.</td><td>แบตเตอรี่วิทยุสื่อสาร</td><td class="text-right text-red-500 font-semibold pr-4">550</td><td class="text-center">บาท</td></tr>
                            <tr><td>3.</td><td>ชุดหูฟังวิทยุสื่อสาร</td><td class="text-right text-red-500 font-semibold pr-4">200</td><td class="text-center">บาท</td></tr>
                            <tr><td>4.</td><td>ชุดแท่นชาร์จวิทยุสื่อสาร</td><td class="text-right text-red-500 font-semibold pr-4">550</td><td class="text-center">บาท</td></tr>
                            <tr><td>5.</td><td>เสาสัญญาณวิทยุสื่อสาร</td><td class="text-right text-red-500 font-semibold pr-4">250</td><td class="text-center">บาท</td></tr>
                            <tr><td>6.</td><td>กระจกหน้าจอวิทยุสื่อสาร</td><td class="text-right text-red-500 font-semibold pr-4">200</td><td class="text-center">บาท</td></tr>
                            <tr><td>7.</td><td>ซองหนัง/คลิปหลังวิทยุสื่อสาร</td><td class="text-right text-red-500 font-semibold pr-4">100</td><td class="text-center">บาท</td></tr>
                        </table>
                        <table>
                            <tr><td>8.</td><td>ยางข้างปุ่มเพิ่มลดเสียง</td><td class="text-right text-red-500 font-semibold pr-4">300</td><td class="text-center">บาท</td></tr>
                            <tr><td>9.</td><td>เคี้ยววิทยุสื่อสารหลุด/หัก</td><td class="text-right text-red-500 font-semibold pr-4">200</td><td class="text-center">บาท</td></tr>
                            <tr><td>10.</td><td>กล่องพลาสติกใหญ่</td><td class="text-right text-red-500 font-semibold pr-4">350</td><td class="text-center">บาท</td></tr>
                            <tr><td>11.</td><td>กล่องพลาสติกเล็ก</td><td class="text-right text-red-500 font-semibold pr-4">200</td><td class="text-center">บาท</td></tr>
                            <tr><td>12.</td><td>กล่องกระดาษใส่แท่นชาร์จ</td><td class="text-right text-red-500 font-semibold pr-4">10</td><td class="text-center">บาท</td></tr>
                            <tr><td>13.</td><td>กล่องกระดาษใส่อุปกรณ์</td><td class="text-right text-red-500 font-semibold pr-4">55</td><td class="text-center">บาท</td></tr>
                        </table>
                    </div>
                '))
        ])->collapsed()->compact()->extraAttributes(['class' => 'bg-lime-100']);
    }

    public static function getBorrowitemsRepeater() {
        return FilamentSection::make('ข้อปฏิบัติในการ ยืม-คืน อุปกรณ์วิทยุสื่อสาร')->headerActions([
                Action::make('reset')
                    ->modalHeading('ลบรายการยืมทั้งหมดจากฟอร์มนี้')
                    ->requiresConfirmation()
                    ->color('danger')
                    ->action(fn (Set $set) => $set('borrowitems', []))
            ])->schema([
                Placeholder::make('guidelines')
                    ->hiddenLabel()
                    ->content(new HtmlString('
                        <style>
                            .gap-6 {gap: 0.5rem; !important}
                            .fi-fo-repeater-item-header {padding-bottom: 0.5rem !important;padding-top: 0.5rem !important;}
                            .guide-ol {list-style: none;counter-reset: num;}
                            .guide-ol .guide-li {counter-increment: num;}
                            .guide-ol .guide-li::before {color: green;font-weight: bold;content: counter(num) ". ";}
                            .guide-li span {color: red;font-weight: bold;}
                        </style>
                        <ol class="guide-ol">
                            <li class="guide-li">ค่ามัดจำ 100 บาทต่อเครื่อง (อัตรานี้ ไม่รวมกรณีใช้งานภายนอกวัดหรือใช้งานต่างประเทศ)</li>
                            <li class="guide-li">หากอุปกรณ์<span>ชำรุดเสียหาย</span>หรือ<span>สูญหาย</span> ผู้ยืมและหน่วยงานที่ยืมจะต้อง<span>รับผิดชอบราคาอุปกรณ์</span>ทั้งหมด</li>
                            <li class="guide-li">หาก<span>คืนอุปกรณ์ล่าช้า</span>กว่ากำหนด ต้องจ่ายค่า<span>ปรับ 100 บาทต่อวัน</span></li>
                            <li class="guide-li">ติดต่อบริการยืม-คืนได้ที่ ศูนย์คอมพิวเตอร์ ตึกไอทีใหม่(ข้างโรงไฟฟ้าย่อย) เบอร์ภายใน 11816</li>
                        </ol>
                    ')),
                TableRepeater::make('borrowitems')
                    ->relationship()
                    ->hiddenLabel()
                    ->headers([
                        Header::make('รายละเอียดอุปกรณ์')->width('210px'),
                        Header::make('จ.ขอ')->align(Alignment::Center)->width('80px'),
                        Header::make('จ.ให้ยืม')->align(Alignment::Center)->width('80px'),
                        Header::make('จ.คืน')->align(Alignment::Center)->width('80px'),
                        Header::make('เลขทะเบียน ว.')->align(Alignment::Center)
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
                            })
                            ->afterStateHydrated(function ($state, Set $set) {
                                $product = Product::find($state);
                                if($product) {
                                    $price_borrow = $product->price_borrow ?? 0;
                                    $price_product = $product->price_product ?? 0;
                                    $price_borrow = intval($price_borrow) == $price_borrow ? intval($price_borrow) : $price_borrow;
                                    $price_product = intval($price_product) == $price_product ? intval($price_product) : $price_product;
                                    $set('price_borrow', $price_borrow);
                                    $set('price_product', $price_product);
                                }
                            })
                            ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                            ->required()
                            ->columnSpan(1),
                        Hidden::make('price_product'),
                        Hidden::make('price_borrow'),
                        TextInput::make('q_request')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(1000)
                            ->live(onBlur: true)
                            ->rules(function(Get $get, ?Model $record = null) {
                                return function($attribute, $value, $fail) use($get, $record) {
                                    $productId = $get('product_id');
                                    $store = Store::where('product_id', $productId)->first();
                                    $qAll = $store->q_all - $store->q_waste - $store->q_borrow;
                                    $qBook = $store->q_book;
                                    if($record) {
                                        $qBook = $qBook - $record->q_request + $value;
                                    } else {
                                        $qBook += $value;
                                    }
                                    if($qBook > $qAll) {
                                        $fail('อุปกรณ์มีไม่พอสำหรับการยืม');
                                    }
                                };
                            })
                            ->afterStateUpdated(function($state, Set $set) {
                                if($state < 0) {$set('q_request', abs($state));}
                            })
                            ->dehydrated()
                            ->required()
                            ->columnSpan(1),
                        TextInput::make('q_lend')
                            ->numeric()
                            ->default(0)
                            ->disabled()
                            ->required()
                            ->columnSpan(1),
                        TextInput::make('q_return')
                            ->numeric()
                            ->default(0)
                            ->disabled()
                            ->columnSpan(1),
                        Hidden::make('total_price_borrow')
                            ->hiddenLabel()
                            ->dehydrated(),
                        Select::make('serials')
                            ->relationship('serials', 'name')
                            ->multiple()
                            ->reactive()
                            ->disabled()
                            ->columnSpan(2)
                    ])->extraItemActions([
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
                    ->addActionLabel('เพิ่มการยืมอุปกรณ์')
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
            ])->compact()->collapsible()
        ;
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
                ->dehydrated()
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
        ])->columns(3)->compact()->collapsible();
    }

    public static function getBorrowasideFormSchema() {
        return FilamentSection::make()->schema([
            TextInput::make('price_borrow_all')
                ->hiddenLabel()
                ->prefix('รวมค่ามัดจำ')
                ->dehydrated()
                ->numeric()
                ->readOnly(),
            TextInput::make('price_fine')
                ->hiddenLabel()
                ->prefix('รวมค่าปรับ')
                ->dehydrated()
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
