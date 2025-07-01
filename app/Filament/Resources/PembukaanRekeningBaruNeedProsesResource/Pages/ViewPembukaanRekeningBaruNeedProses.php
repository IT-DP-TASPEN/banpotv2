<?php

namespace App\Filament\Resources\PembukaanRekeningBaruNeedProsesResource\Pages;

use App\Filament\Resources\PembukaanRekeningBaruNeedProsesResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewPembukaanRekeningBaruNeedProses extends ViewRecord
{
    protected static string $resource = PembukaanRekeningBaruNeedProsesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
