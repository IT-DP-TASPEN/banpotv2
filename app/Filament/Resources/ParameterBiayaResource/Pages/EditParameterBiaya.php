<?php

namespace App\Filament\Resources\ParameterBiayaResource\Pages;

use App\Filament\Resources\ParameterBiayaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditParameterBiaya extends EditRecord
{
    protected static string $resource = ParameterBiayaResource::class;

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
