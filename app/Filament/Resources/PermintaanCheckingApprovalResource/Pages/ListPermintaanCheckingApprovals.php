<?php

namespace App\Filament\Resources\PermintaanCheckingApprovalResource\Pages;

use App\Filament\Resources\PermintaanCheckingApprovalResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPermintaanCheckingApprovals extends ListRecords
{
    protected static string $resource = PermintaanCheckingApprovalResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}