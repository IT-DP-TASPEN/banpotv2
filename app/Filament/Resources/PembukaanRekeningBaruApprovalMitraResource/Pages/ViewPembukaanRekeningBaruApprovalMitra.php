<?php

namespace App\Filament\Resources\PembukaanRekeningBaruApprovalMitraResource\Pages;

use App\Filament\Resources\PembukaanRekeningBaruApprovalMitraResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewPembukaanRekeningBaruApprovalMitra extends ViewRecord
{
    protected static string $resource = PembukaanRekeningBaruApprovalMitraResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
