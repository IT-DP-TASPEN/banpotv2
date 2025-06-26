<?php

namespace App\Filament\Resources\FlaggingMutasiTifApprovalMitraResource\Pages;

use App\Filament\Resources\FlaggingMutasiTifApprovalMitraResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListFlaggingMutasiTifApprovalMitras extends ListRecords
{
    protected static string $resource = FlaggingMutasiTifApprovalMitraResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}