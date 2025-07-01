<?php

namespace App\Filament\Resources\PembukaanRekeningBaruNeedProsesResource\Pages;

use App\Filament\Resources\PembukaanRekeningBaruNeedProsesResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPembukaanRekeningBaruNeedProses extends EditRecord
{
    protected static string $resource = PembukaanRekeningBaruNeedProsesResource::class;

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
