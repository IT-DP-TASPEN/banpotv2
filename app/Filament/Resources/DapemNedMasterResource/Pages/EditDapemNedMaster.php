<?php

namespace App\Filament\Resources\DapemNedMasterResource\Pages;

use App\Filament\Resources\DapemNedMasterResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDapemNedMaster extends EditRecord
{
    protected static string $resource = DapemNedMasterResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }
}
