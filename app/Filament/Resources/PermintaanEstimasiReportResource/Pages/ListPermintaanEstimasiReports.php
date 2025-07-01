<?php

namespace App\Filament\Resources\PermintaanEstimasiReportResource\Pages;

use Filament\Actions;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PermintaanEstimasiExport;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\PermintaanEstimasiReportResource;

class ListPermintaanEstimasiReports extends ListRecords
{
    protected static string $resource = PermintaanEstimasiReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('exportpermintaanestimasi')
                ->label('Export')
                ->icon('heroicon-o-document-arrow-down')
                ->color('primary')
                ->action(function () {
                    $estimasi = $this->getFilteredTableQuery();
                    $this->applySortingToTableQuery($estimasi);

                    $fileName = 'report_permintaan_estimasi_' . date('Ymd_His') . '.xlsx';

                    return Excel::download(new PermintaanEstimasiExport($estimasi), $fileName);
                }),
        ];
    }
}
