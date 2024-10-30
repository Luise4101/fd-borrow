<?php

namespace App\Filament\Resources\Account\RoleResource\RelationManagers;

use Filament\Tables\Table;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\AttachAction;
use Filament\Tables\Actions\DetachAction;
use Filament\Tables\Enums\ActionsPosition;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DetachBulkAction;
use Filament\Resources\RelationManagers\RelationManager;

class UsersRelationManager extends RelationManager
{
    protected static string $relationship = 'users';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('id')
                    ->label(__('User ID'))
                    ->toggleable()
                    ->sortable(),
                TextColumn::make('name')
                    ->label(__('User หน้าแดง'))
                    ->toggleable()
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email')
                    ->label(__('อีเมล'))
                    ->toggleable()
                    ->searchable()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label(__('ล็อกอินครั้งแรก'))
                    ->toggleable()
                    ->sortable()
                    ->date('j F Y'),
                TextColumn::make('updated_at')
                    ->label(__('ล็อกอินล่าสุด'))
                    ->toggleable()
                    ->sortable()
                    ->date('j F Y')
            ])
            ->heading('ผู้ใช้ที่รับบทบาทนี้')
            ->headerActions([
                AttachAction::make()->preloadRecordSelect()->form(fn(AttachAction $action): array => [
                    $action->getRecordSelect()
                ])
            ])
            ->actions([
                EditAction::make(),
                DetachAction::make()
            ], position: ActionsPosition::BeforeCells)
            ->bulkActions([
                BulkActionGroup::make([ DetachBulkAction::make() ])
            ]);
    }
}
