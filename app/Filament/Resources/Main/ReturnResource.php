<?php

namespace App\Filament\Resources\Main;

use App\Models\User;
use Filament\Tables;
use Filament\Forms\Set;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Enums\ReturnStatus;
use Filament\Support\RawJs;
use Filament\Facades\Filament;
use App\Models\Main\BorrowHead;
use App\Models\Main\BorrowItem;
use App\Models\Main\ReturnHead;
use App\Models\Inventory\Serial;
use Filament\Resources\Resource;
use App\Models\Inventory\Product;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Facades\Log;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use App\Http\Controllers\HRController;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Grid as FormGrid;
use App\Filament\Resources\Main\ReturnResource\Pages;
use Filament\Forms\Components\Section as FormSection;

class ReturnResource extends Resource
{
    protected static ?string $model = ReturnHead::class;
    protected static ?string $modelLabel = 'รายการคืนวิทยุสื่อสาร';
    protected static ?string $navigationLabel = '4.3 จัดการส่งคืนวิทยุสื่อสาร';
    protected static ?string $navigationIcon = 'heroicon-o-arrow-uturn-left';
    protected function afterSave($record) {
        // Log the saved record
        Log::info('Saved Record:', [$record]);

        // Access related returnitems
        foreach ($record->returnitems as $returnItem) {
            Log::info('Processing ReturnItem:', [$returnItem->toArray()]);

            // Detach all serials first
            $returnItem->serials()->detach();

            // Sync returnedSerials
            if (!empty($returnItem->returnedSerials)) {
                Log::info('Syncing Returned Serials:', $returnItem->returnedSerials);
                $returnItem->serials()->syncWithPivotValues(
                    $returnItem->returnedSerials,
                    ['status_id' => 21]
                );
            }

            // Sync brokenSerials
            if (!empty($returnItem->brokenSerials)) {
                Log::info('Syncing Broken Serials:', $returnItem->brokenSerials);
                $returnItem->serials()->syncWithPivotValues(
                    $returnItem->brokenSerials,
                    ['status_id' => 22]
                );
            }

            // Sync missingSerials
            if (!empty($returnItem->missingSerials)) {
                Log::info('Syncing Missing Serials:', $returnItem->missingSerials);
                $returnItem->serials()->syncWithPivotValues(
                    $returnItem->missingSerials,
                    ['status_id' => 23]
                );
            }
        }
    }

    public static function form(Form $form): Form {
        return $form->schema([
            static::getReturnheadFormSchema(),
            static::getReturnitemsRepeater(),
            FormSection::make()->schema([Textarea::make('note')->label('รายละเอียดอื่น ๆ')->maxLength(500)->autosize()])
        ]);
    }

    public static function table(Table $table): Table {
        return $table
            ->columns([
                TextColumn::make('borrow_head_id')
                    ->label('BID')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('id')
                    ->label('ID')
                    ->toggleable()
                    ->searchable()
                    ->sortable(),
                TextColumn::make('status.name')
                    ->label('สถานะการคืน')
                    ->badge()
                    ->color(function(string $state): string {
                        return match($state) {
                            'ตรวจสอบอุปกรณ์ที่คืน' => 'warning',
                            'คืนอุปกรณ์เสร็จสิ้น' => 'success'
                        };
                    })
                    ->icon(function(string $state): string {
                        return match($state) {
                            'ตรวจสอบอุปกรณ์ที่คืน' => 'heroicon-m-arrow-path',
                            'คืนอุปกรณ์เสร็จสิ้น' => 'heroicon-m-shield-check'
                        };
                    })
                    ->toggleable()
                    ->searchable()
                    ->sortable(),
                TextColumn::make('display_username')
                    ->label('ผู้ส่งคืน')
                    ->toggleable()
                    ->searchable()
                    ->sortable(),
                TextColumn::make('received_by')
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make()
            ]);
    }

    public static function getRelations(): array {
        return [];
    }

    public static function getPages(): array {
        return [
            'index' => Pages\ListReturns::route('/'),
            'create' => Pages\CreateReturn::route('/create'),
            'edit' => Pages\EditReturn::route('/{record}/edit'),
        ];
    }

    public static function getReturnheadFormSchema() {
        return FormGrid::make(2)->schema([
            FormSection::make(new HtmlString('
                <style>
                    .gap-6 {gap: 0.75rem; !important}
                    .bg-color{background-color:#d9edf6;}
                    .bg-lime-100 {background-color: #ecfccb;}
                </style>
            '))->schema([
                Select::make('borrow_head_id')
                    ->relationship('borrowhead', 'id')
                    ->hiddenLabel()
                    ->prefix('BID')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(function(callable $set, $state) {
                        if($state) {
                            $borrowhead = BorrowHead::find($state);
                            $aduser = User::find($borrowhead?->borrower_id);
                            $userdata = (new HRController())->fetchApproverData($aduser->name);
                            $set('user_id', $borrowhead?->borrower_id);
                            $set('user_tel', $userdata->mobile);
                            $set('user_lineid', $userdata->lineid);
                        }
                    }),
                ToggleButtons::make('status_id')
                    ->options(ReturnStatus::class)
                    ->hiddenLabel()
                    ->default(19)
                    ->required()
                    ->inline()
                    ->columnSpan(2),
                TextInput::make('price_fine_all')
                    ->prefix('รวมค่าปรับ')
                    ->hiddenLabel()
                    ->dehydrated()
                    ->readOnly()
                    ->default(0)
                    ->numeric()
                    ->columnSpanFull(),
                Select::make('received_by')
                    ->default(Filament::auth()->id())
                    ->disabled()
                    ->relationship('user', 'name')
                    ->prefix('เจ้าหน้าที่รับคืน')
                    ->hiddenLabel()
                    ->columnSpanFull(),
                DateTimePicker::make('created_at')
                    ->hiddenLabel()
                    ->prefix('วันที่คืนอุปกรณ์')
                    ->visibleOn('edit')
                    ->firstDayOfWeek(7)
                    ->displayFormat('j F Y H:i')
                    ->disabled()
                    ->columnSpanFull(),
                DateTimePicker::make('updated_at')
                    ->hiddenLabel()
                    ->prefix('วันที่อัปเดต')
                    ->visibleOn('edit')
                    ->firstDayOfWeek(7)
                    ->displayFormat('j F Y H:i')
                    ->disabled()
                    ->columnSpanFull()
            ])->extraAttributes(['class' => 'bg-color'])->compact()->columns(3)->columnSpan(1),
            FormSection::make()->schema([
                Radio::make('user_choice')
                    ->options([
                        'user_id' => 'AD User',
                        'user_fullname' => 'อาสาสมัคร'
                    ])
                    ->default('user_id')
                    ->label('ผู้ส่งคืน')
                    ->reactive()
                    ->inline()
                    ->afterStateUpdated(fn(callable $set) => [
                        $set('user_id', null),
                        $set('user_fullname', null),
                        $set('user_tel', null),
                        $set('user_lineid', null)
                    ])
                    ->afterStateHydrated(function($get, callable $set) {
                        if($get('user_id') || $get('user_fullname')) {
                            $set('user_choice', $get('user_id') ? 'user_id' : 'user_fullname');
                        }
                    }),
                Select::make('user_id')
                    ->required(fn($get) => $get('user_choice') === 'user_id')
                    ->visible(fn($get) => $get('user_choice') === 'user_id')
                    ->relationship('user', 'name')
                    ->placeholder('เลือกผู้ส่งคืน')
                    ->prefix('AD User')
                    ->hiddenLabel()
                    ->searchable()
                    ->preload()
                    ->live(onBlur: true)
                    ->afterStateUpdated(function(callable $set, $state) {
                        if($state) {
                            $aduser = User::find($state);
                            $userdata = (new HRController())->fetchApproverData($aduser->name);
                            $set('user_tel', $userdata->mobile);
                            $set('user_lineid', $userdata->lineid);
                        }
                    }),
                TextInput::make('user_fullname')
                    ->required(fn($get) => $get('user_choice') === 'user_fullname')
                    ->visible(fn($get) => $get('user_choice') === 'user_fullname')
                    ->placeholder('กรอกชื่อ-นามสกุล ผู้ส่งคืน')
                    ->prefix('ชื่อผู้ส่งคืน')
                    ->hiddenLabel(),
                TextInput::make('user_tel')
                    ->mask(RawJs::make(<<<'JS'
                        $input.split(',').map(tel => {
                            tel = tel.trim();
                            return tel.startsWith('02') ? tel.replace(/(\d{2})(\d{3})(\d{4})/, '$1-$2-$3') : tel.replace(/(\d{3})(\d{3})(\d{4})/, '$1-$2-$3');
                        }).join(', ');
                    JS))
                    ->dehydrateStateUsing(function($state) {
                        return collect(explode(',', $state))->map(function($tel) {
                            return preg_replace('/[^\d]/', '', $tel);
                        })->implode(', ');
                    })
                    ->placeholder('000-000-0000')
                    ->prefix('เบอร์โทร')
                    ->maxLength(40)
                    ->hiddenLabel(),
                TextInput::make('user_lineid')
                    ->prefix(__('Line ID'))
                    ->hiddenLabel()
                    ->maxLength(50)
            ])->extraAttributes(['class' => 'bg-color'])->compact()->columnSpan(1)
        ]);
    }

    public static function getReturnitemsRepeater() {
        return FormSection::make('รายการอุปกรณ์ที่คืน')->headerActions([
            Action::make('reset')
                ->action(fn(Set $set) => $set('returnitems', []))
                ->modalHeading('ลบรายการคืนทั้งหมดจากฟอร์มนี้')
                ->requiresConfirmation()
                ->color('danger')
        ])->schema([
            Repeater::make('returnitems')
                ->orderColumn('sort')
                ->relationship()
                ->hiddenLabel()
                ->schema([
                    Select::make('borrow_item_id')
                        ->relationship('borrowitem', 'id')
                        ->options(function($get, $set, $state, $record) {
                            $borrowheadId = $get('../../borrow_head_id');
                            if($borrowheadId) {
                                return BorrowItem::where('borrow_head_id', $borrowheadId)
                                    ->whereColumn('q_all_return', '<', 'q_lend')
                                    ->join('products', 'borrow_items.product_id', '=', 'products.id')
                                    ->pluck('products.name', 'borrow_items.id');
                            } else {
                                return [];
                            }
                        })
                        ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                        ->placeholder('เลือกอุปกรณ์ที่ส่งคืน')
                        ->hiddenLabel()
                        ->searchable()
                        ->preload()
                        ->required()
                        ->afterStateUpdated(fn(callable $set, $state) => self::setValueBI($set, $state))
                        ->afterStateHydrated(fn(callable $set, $state) => self::setValueBI($set, $state)),
                    Hidden::make('price_product'),
                    TextInput::make('q_borrowed')
                        ->prefix('จำนวนที่ยืม')
                        ->hiddenLabel()
                        ->readonly()
                        ->numeric(),
                    TextInput::make('q_return')
                        ->prefix('จำนวนที่คืน')
                        ->hiddenLabel()
                        ->minValue(0)
                        ->required()
                        ->default(0)
                        ->numeric()
                        ->rule(function($get) {
                            return function(string $attribute, $value, $fail) use($get) {
                                self::validateQty($get, 'q_return', $value, $fail);
                            };
                        }),
                    Select::make('returnedSerials')
                        ->relationship('returnedSerials', 'name')
                        ->prefix('เลขทะเบียนวิทยุสื่อสารที่คืน')
                        ->live(onBlur: true)
                        ->hiddenLabel()
                        ->searchable()
                        ->multiple()
                        ->preload()
                        ->options(fn($get) => self::manageSerialOptions($get, 'returnedSerials')),
                    TextInput::make('q_broken')
                        ->prefix('จำนวนที่เสีย')
                        ->hiddenLabel()
                        ->minValue(0)
                        ->required()
                        ->default(0)
                        ->numeric()
                        ->rule(function($get) {
                            return function(string $attribute, $value, $fail) use($get) {
                                self::validateQty($get, 'q_broken', $value, $fail);
                            };
                        }),
                    Select::make('brokenSerials')
                        ->relationship('brokenSerials', 'name')
                        ->prefix('เลขทะเบียนวิทยุสื่อสารที่เสีย')
                        ->live(onBlur: true)
                        ->hiddenLabel()
                        ->searchable()
                        ->multiple()
                        ->preload()
                        ->options(fn($get) => self::manageSerialOptions($get, 'brokenSerials')),
                    TextInput::make('fine_broken')
                        ->placeholder('อุปกรณืที่เสีย')
                        ->prefix('ค่าปรับ')
                        ->hiddenLabel()
                        ->required()
                        ->default(0)
                        ->numeric(),
                    TextInput::make('q_missing')
                        ->prefix('จำนวนที่หาย')
                        ->hiddenLabel()
                        ->minValue(0)
                        ->required()
                        ->default(0)
                        ->numeric()
                        ->live(onBlur: true)
                        ->rule(function($get) {
                            return function(string $attribute, $value, $fail) use($get) {
                                self::validateQty($get, 'q_missing', $value, $fail);
                            };
                        })
                        ->afterStateUpdated(fn(callable $set, $state, $get) =>
                            $set('fine_missing', (float)($get('price_product') ?? 0) * (int)$state)
                        ),
                    Select::make('missingSerials')
                        ->relationship('missingSerials', 'name')
                        ->prefix('เลขทะเบียนวิทยุสื่อสารที่หาย')
                        ->live(onBlur: true)
                        ->hiddenLabel()
                        ->searchable()
                        ->multiple()
                        ->preload()
                        ->options(fn($get) => self::manageSerialOptions($get, 'missingSerials')),
                    TextInput::make('fine_missing')
                        ->placeholder('อุปกรณ์ที่หาย')
                        ->prefix('ค่าปรับ')
                        ->hiddenLabel()
                        ->dehydrated()
                        ->default(0)
                        ->readonly(),
                    TextArea::make('note')
                        ->label('รายละเอียดอื่น ๆ')
                        ->columnSpanFull()
                        ->maxLength(250)
                        ->autosize()
                ])->columns(2)
        ])->extraAttributes(['class' => 'bg-lime-100'])->compact();
    }

    public static function setValueBI(callable $set, $state): void {
        if($state) {
            $borrowItem = BorrowItem::select('product_id', 'q_lend', 'q_all_return')
                ->with('product:id,price_product')
                ->find($state);
            $priceProduct = $borrowItem?->product?->price_product ?? 0;
            $remainingQty = $borrowItem? ($borrowItem->q_lend - $borrowItem->q_all_return): 0;
            $set('price_product', $priceProduct);
            $set('q_borrowed', $remainingQty);
        } else {
            $set('price_product', 0);
            $set('q_borrowed', 0);
        }
    }
    public static function validateQty(callable $get, string $currentField, $value, callable $fail): void {
        $qReturn = $currentField === 'q_return' ? $value : $get('q_return') ?? 0;
        $qBroken = $currentField === 'q_broken' ? $value : $get('q_broken') ?? 0;
        $qMissing = $currentField === 'q_missing' ? $value : $get('q_missing') ?? 0;
        $qBorrowed = $get('q_borrowed') ?? 0;
        $total = $qReturn + $qBroken + $qMissing;
        if($total > $qBorrowed) {
            $fail("อุปกรณ์ที่นำมาส่งคืน {{$total}} ไม่ตรงกับจำนวนที่ยืม {{$qBorrowed}}");
        }
    }
    public static function manageSerialOptions($get, $currentField): array {
        $borrowItem = BorrowItem::find($get('borrow_item_id'));
        $allSerials = $borrowItem?->serials->pluck('name', 'id')->toArray() ?? [];
        $selectedSerials = array_merge(
            $get('returnedSerials') ?? [],
            $get('brokenSerials') ?? [],
            $get('missingSerials') ?? []
        );
        $selectedSerials = array_diff($selectedSerials, $get($currentField) ?? []);
        $availableOptions = array_diff_key($allSerials, array_flip($selectedSerials));
        return $availableOptions;
    }
}
