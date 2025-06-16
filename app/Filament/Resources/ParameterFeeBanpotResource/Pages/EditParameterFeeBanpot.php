<?php

namespace App\Filament\Resources\ParameterFeeBanpotResource\Pages;

use App\Filament\Resources\ParameterFeeBanpotResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditParameterFeeBanpot extends EditRecord
{
    protected static string $resource = ParameterFeeBanpotResource::class;

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
