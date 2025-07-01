<?php

namespace App\Filament\Resources\BanpotMasterCompletedResource\Pages;

use App\Filament\Resources\BanpotMasterCompletedResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewBanpotMasterCompleted extends ViewRecord
{
    protected static string $resource = BanpotMasterCompletedResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->visible(function () {
                    $user = auth()->user();
                    return $user->isAdmin() || $user->isSuperAdmin() || $user->isStaffBankDPTaspen() || $user->isApprovalBankDPTaspen();
                }),
        ];
    }
}
