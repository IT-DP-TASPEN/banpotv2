<?php

namespace App\Filament\Resources\PembukaanRekeningBaruReportResource\Pages;

use App\Filament\Resources\PembukaanRekeningBaruReportResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPembukaanRekeningBaruReport extends EditRecord
{
    protected static string $resource = PembukaanRekeningBaruReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
        ];
    }
}