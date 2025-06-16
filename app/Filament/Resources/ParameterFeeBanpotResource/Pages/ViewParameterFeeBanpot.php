<?php

namespace App\Filament\Resources\ParameterFeeBanpotResource\Pages;

use App\Filament\Resources\ParameterFeeBanpotResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewParameterFeeBanpot extends ViewRecord
{
    protected static string $resource = ParameterFeeBanpotResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
