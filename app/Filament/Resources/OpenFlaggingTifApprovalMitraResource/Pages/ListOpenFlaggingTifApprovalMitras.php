<?php

namespace App\Filament\Resources\OpenFlaggingTifApprovalMitraResource\Pages;

use App\Filament\Resources\OpenFlaggingTifApprovalMitraResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListOpenFlaggingTifApprovalMitras extends ListRecords
{
    protected static string $resource = OpenFlaggingTifApprovalMitraResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}