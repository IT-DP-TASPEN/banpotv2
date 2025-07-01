<?php

namespace App\Filament\Resources\PembukaanRekeningBaruNeedProsesResource\Pages;

use App\Filament\Resources\PembukaanRekeningBaruNeedProsesResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPembukaanRekeningBaruNeedProses extends ListRecords
{
    protected static string $resource = PembukaanRekeningBaruNeedProsesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
