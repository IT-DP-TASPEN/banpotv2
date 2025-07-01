<?php

namespace App\Filament\Resources\PermintaanMutasiTosReportResource\Pages;

use Filament\Actions;
use App\Exports\MutasiTosExport;
use Maatwebsite\Excel\Facades\Excel;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\PermintaanMutasiTosReportResource;

class ListPermintaanMutasiTosReports extends ListRecords
{
    protected static string $resource = PermintaanMutasiTosReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('exportmutasitos')
                ->label('Export')
                ->icon('heroicon-o-document-arrow-down')
                ->color('primary')
                ->action(function () {
                    $mutasitos = $this->getFilteredTableQuery();
                    $this->applySortingToTableQuery($mutasitos);

                    $fileName = 'report_permintaan_mutasi_tos_' . date('Ymd_His') . '.xlsx';

                    return Excel::download(new MutasiTosExport($mutasitos), $fileName);
                }),
        ];
    }
}
