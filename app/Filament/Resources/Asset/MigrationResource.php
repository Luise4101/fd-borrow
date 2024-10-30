<?php

namespace App\Filament\Resources\Asset;

use Filament\Tables\Table;
use App\Models\Asset\Migration;
use Filament\Resources\Resource;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Enums\ActionsPosition;
use Filament\Tables\Actions\DeleteBulkAction;
use App\Filament\Resources\Asset\MigrationResource\Pages\ListMigrations;

class MigrationResource extends Resource
{
    protected static ?string $modelLabel = '';
    protected static ?string $model = Migration::class;
    protected static ?string $navigationLabel = '2.6 แผนผัง';
    protected static ?string $navigationIcon = 'heroicon-o-cloud-arrow-up';
    protected static ?string $activeNavigationIcon = 'heroicon-s-cloud-arrow-up';

    public static function table(Table $table): Table {
        return $table
            ->columns([
                TextColumn::make('id')->sortable(),
                TextColumn::make('migration'),
                TextColumn::make('batch')
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make()
            ], position: ActionsPosition::BeforeCells)
            ->bulkActions([DeleteBulkAction::make()])
            ->defaultPaginationPageOption(50);
    }

    public static function getPages(): array {
        return ['index' => ListMigrations::route('/')];
    }
}
