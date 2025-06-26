<?php

namespace App\Filament\Resources\PembukaanRekeningBaruApprovalMitraResource\Pages;

use App\Filament\Resources\PembukaanRekeningBaruApprovalMitraResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPembukaanRekeningBaruApprovalMitra extends EditRecord
{
    protected static string $resource = PembukaanRekeningBaruApprovalMitraResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
        ];
    }
}