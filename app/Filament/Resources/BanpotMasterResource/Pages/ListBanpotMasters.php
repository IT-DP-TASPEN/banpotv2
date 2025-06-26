<?php

namespace App\Filament\Resources\BanpotMasterResource\Pages;

use App\Exports\PembukaanRekeningBaruNasabahMasterExport;
use App\Filament\Imports\BanpotMasterImporter;
use App\Filament\Resources\BanpotMasterResource;
use App\Imports\BanpotMasterImport;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use EightyNine\ExcelImport\ExcelImportAction;
use Filament\Actions\Action;
use Maatwebsite\Excel\Exporter;
use Maatwebsite\Excel\Facades\Excel;

class ListBanpotMasters extends ListRecords
{
    protected static string $resource = BanpotMasterResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ExcelImportAction::make()
                ->color("primary")
                ->label('Import Banpot Master')
                ->use(BanpotMasterImport::class),
            Actions\CreateAction::make(),
        ];
    }
}