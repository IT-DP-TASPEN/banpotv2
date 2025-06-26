<?php

namespace App\Filament\Resources\PembukaanRekeningBaruApprovalMitraResource\Pages;

use App\Filament\Resources\PembukaanRekeningBaruApprovalMitraResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPembukaanRekeningBaruApprovalMitras extends ListRecords
{
    protected static string $resource = PembukaanRekeningBaruApprovalMitraResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}