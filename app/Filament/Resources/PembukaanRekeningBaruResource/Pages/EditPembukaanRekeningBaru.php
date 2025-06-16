<?php

namespace App\Filament\Resources\PembukaanRekeningBaruResource\Pages;

use App\Filament\Resources\PembukaanRekeningBaruResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPembukaanRekeningBaru extends EditRecord
{
    protected static string $resource = PembukaanRekeningBaruResource::class;

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
