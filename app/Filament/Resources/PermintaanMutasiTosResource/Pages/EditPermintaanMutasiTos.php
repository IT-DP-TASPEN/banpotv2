<?php

namespace App\Filament\Resources\PermintaanMutasiTosResource\Pages;

use App\Filament\Resources\PermintaanMutasiTosResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPermintaanMutasiTos extends EditRecord
{
    protected static string $resource = PermintaanMutasiTosResource::class;

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
