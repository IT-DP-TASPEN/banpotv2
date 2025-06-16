<?php

namespace App\Filament\Resources\MitraCabangMasterResource\Pages;

use App\Filament\Resources\MitraCabangMasterResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMitraCabangMaster extends EditRecord
{
    protected static string $resource = MitraCabangMasterResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
