<?php

namespace App\Filament\Resources\DapemNedMasterResource\Pages;

use App\Filament\Resources\DapemNedMasterResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewDapemNedMaster extends ViewRecord
{
    protected static string $resource = DapemNedMasterResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
