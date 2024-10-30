<?php

namespace App\Filament\Resources\Asset\MigrationResource\Pages;

use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\Asset\MigrationResource;

class ListMigrations extends ListRecords {
    protected static string $resource = MigrationResource::class;
}
