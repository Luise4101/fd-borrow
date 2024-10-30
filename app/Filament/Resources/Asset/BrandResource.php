<?php

namespace App\Filament\Resources\Asset;

use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\Asset\Brand;
use Illuminate\Support\Str;
use Filament\Resources\Resource;
use Illuminate\Http\UploadedFile;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Section;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\ImageColumn;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Enums\ActionsPosition;
use App\Filament\Resources\Asset\BrandResource\Pages;

class BrandResource extends Resource
{
    protected static ?string $model = Brand::class;
    protected static ?string $modelLabel = 'แบรนด์ ';
    protected static ?string $navigationLabel = '2.3 แบรนด์';
    protected static ?string $navigationIcon = 'heroicon-o-tag';
    protected static ?string $activeNavigationIcon = 'heroicon-s-tag';

    public static function form(Form $form): Form
    {
        return $form ->schema([
            Section::make()->columns(3)->schema([
                TextInput::make('name')
                    ->hiddenLabel()
                    ->prefix(__('ชื่อแบรนด์'))
                    ->maxLength(100)
                    ->required()
                    ->columnSpan(2),
                Toggle::make('active')
                    ->label(__('การใช้งาน'))
                    ->default(1)
                    ->onIcon('heroicon-m-check')
                    ->offIcon('heroicon-m-x-mark')
                    ->onColor('success')
                    ->offColor('danger'),
                FileUpload::make('img')
                    ->label(__('โลโก้'))
                    ->directory('images/brand')
                    ->imageEditor()
                    ->imageEditorAspectRatios([null,'16:9','4:3','1:1'])
                    ->getUploadedFileNameForStorageUsing(fn(UploadedFile $file) => Date('YmdHis').'_Brand_'.Str::random(5).'.'.$file->getClientOriginalExtension())
                    ->openable()
                    ->columnSpanFull()
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
                    ->label(__('ชื่อแบรนด์'))
                    ->toggleable()
                    ->searchable()
                    ->sortable(),
                ImageColumn::make('img')
                    ->label(__('ภาพโลโก้'))
                    ->toggleable(),
                TextColumn::make('created_at')
                    ->label(__('วันที่สร้าง'))
                    ->toggleable(isToggledHiddenByDefault:true)
                    ->sortable()
                    ->date('j F Y'),
                TextColumn::make('updated_at')
                    ->label(__('วันที่อัปเดต'))
                    ->toggleable(isToggledHiddenByDefault:true)
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
            ->actions([
                EditAction::make(),
                DeleteAction::make()
            ], position: ActionsPosition::BeforeCells);
    }

    public static function getPages(): array {
        return [ 'index' => Pages\ListBrands::route('/') ];
    }
}
