<?php

namespace App\Filament\Resources\PembukaanRekeningBaruResource\Pages;

use App\Filament\Resources\PembukaanRekeningBaruResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewPembukaanRekeningBaru extends ViewRecord
{
    protected static string $resource = PembukaanRekeningBaruResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
