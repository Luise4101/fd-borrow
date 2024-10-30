<?php

namespace App\Filament\Resources\Asset;

use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Support\RawJs;
use App\Models\Asset\Supplier;
use Filament\Resources\Resource;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Enums\ActionsPosition;
use App\Filament\Resources\Asset\SupplierResource\Pages;

class SupplierResource extends Resource
{
    protected static ?string $model = Supplier::class;
    protected static ?string $modelLabel = 'ข้อมูลร้าน ';
    protected static ?string $navigationLabel = '2.4 ข้อมูลร้าน';
    protected static ?string $navigationIcon = 'heroicon-o-building-storefront';
    protected static ?string $activeNavigationIcon = 'heroicon-s-building-storefront';

    public static function form(Form $form): Form
    {
        return $form ->columns(3)->schema([
            Section::make()->columnSpan(['lg' => 2])->schema([
                TextInput::make('name')
                    ->label(__('ชื่อร้าน'))
                    ->maxLength(100)
                    ->required(),
                Textarea::make('location')
                    ->label(__('ที่อยู่'))
                    ->placeholder(__('ที่อยู่ของร้าน'))
                    ->autosize()
                    ->maxLength(255),
                Textarea::make('note')
                    ->label(__('รายละเอียดเพิ่มเติม'))
                    ->placeholder(__('ความคิดเห็นต่อร้าน ฯลฯ'))
                    ->autosize()
                    ->maxLength(500)
            ]),
            Section::make()->columnSpan(['lg' => 1])->schema([
                TextInput::make('email')
                    ->label(__('อีเมล'))
                    ->placeholder(__('example@email.com'))
                    ->email()
                    ->maxLength(100)
                    ->required(),
                TextInput::make('tel')
                    ->label(__('เบอร์โทร'))
                    ->placeholder(__('000-000-0000'))
                    ->tel()
                    ->mask(RawJs::make(<<<'JS'
                        $input.startsWith('02') ? '99-999-9999' : '999-999-9999'
                    JS)),
                TextInput::make('lineid')
                    ->label(__('Line ID'))
                    ->maxLength(50)
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
                    ->label(__('ชื่อร้าน'))
                    ->toggleable()
                    ->searchable()
                    ->sortable(),
                TextColumn::make('อีเมล')
                    ->toggleable()
                    ->searchable()
                    ->sortable(),
                TextColumn::make('location')
                    ->label(__('ที่อยู่'))
                    ->toggleable(isToggledHiddenByDefault:true)
                    ->searchable()
                    ->sortable(),
                TextColumn::make('tel')
                    ->label(__('เบอร์โทร'))
                    ->toggleable()
                    ->searchable()
                    ->sortable(),
                TextColumn::make('lineid')
                    ->label(__('Line ID'))
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
                    ->toggleable(isToggledHiddenByDefault:true)
                    ->sortable()
                    ->date('j F Y')
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make()
            ], position: ActionsPosition::BeforeCells);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSuppliers::route('/'),
            'create' => Pages\CreateSupplier::route('/create'),
            'edit' => Pages\EditSupplier::route('/{record}/edit')
        ];
    }
}
