<?php

namespace App\Filament\Resources\BanpotMasterResource\Pages;

use Filament\Actions;
use App\Imports\BanpotMasterImport;
use App\Jobs\ImportBanpotMasterJob;
use Filament\Notifications\Notification;
use Filament\Forms\Components\FileUpload;
use Filament\Resources\Pages\ListRecords;
use EightyNine\ExcelImport\ExcelImportAction;
use Filament\Resources\Pages\ListRecords\Tab;
use App\Filament\Resources\BanpotMasterResource;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

class ListBanpotMasters extends ListRecords
{
    protected static string $resource = BanpotMasterResource::class;
    protected function getHeaderActions(): array
    {
        set_time_limit(1200);

        return [
            ExcelImportAction::make()
                ->color('primary')
                ->label('Import Banpot Master')
                ->use(BanpotMasterImport::class),
            Actions\CreateAction::make(),
        ];
    }
}
