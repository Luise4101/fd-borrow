<?php

namespace App\Filament\Resources\Asset;

use Filament\Forms\Form;
use App\Models\Asset\Kong;
use Filament\Tables\Table;
use App\Models\Asset\Section;
use Filament\Resources\Resource;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Enums\ActionsPosition;
use App\Filament\Resources\Asset\DepartmentResource\Pages\ListDepartments;

class DepartmentResource extends Resource
{
    protected static ?string $modelLabel = '';
    protected static ?string $model = Kong::class;
    protected static ?string $navigationLabel = '2.5 เครดิต';
    protected static ?string $navigationIcon = 'heroicon-o-map-pin';
    protected static ?string $activeNavigationIcon = 'heroicon-s-map-pin';

    public static function form(Form $form): Form {
        return $form->schema([
            TextInput::make('credit')
                ->prefix(__('เครดิต'))
                ->hiddenLabel()
                ->default(0)
                ->numeric()
        ]);
    }

    public static function table(Table $table): Table {
        return $table->columns([
                TextColumn::make('id')
                    ->toggleable(isToggledHiddenByDefault:true)
                    ->label(__('Kong ID'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('samnak.csamnak')
                    ->label(__('สำนัก'))
                    ->toggleable()
                    ->searchable()
                    ->sortable(),
                TextColumn::make('section.csection')
                    ->label(__('ฝ่าย'))
                    ->toggleable()
                    ->searchable()
                    ->sortable(),
                TextColumn::make('ckong')
                    ->label(__('กอง'))
                    ->toggleable()
                    ->searchable()
                    ->sortable(),
                TextColumn::make('credit')
                    ->label(__('เครดิต'))
                    ->toggleable()
                    ->sortable()
            ])->filters([
                SelectFilter::make('samnak_id')
                    ->relationship('samnak', 'csamnak')
                    ->label(__('สำนัก'))
                    ->native(false),
                SelectFilter::make('section_id')
                    ->options(function() {
                        $samnakid = request()->input('tableFilters.samnak_id');
                        return $samnakid ? Section::where('samnak_id', $samnakid)->pluck('csection', 'id') : Section::pluck('csection', 'id');
                    })
                    ->label(__('ฝ่าย'))
                    ->native(false)
            ])->actions([
                EditAction::make(),
            ], position:ActionsPosition::BeforeCells)
            ->defaultSort('id', 'desc');
    }

    public static function getPages(): array {
        return ['index' => ListDepartments::route('/')];
    }
}
