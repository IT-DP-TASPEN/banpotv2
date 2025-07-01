<?php

namespace App\Filament\Resources\PembukaanRekeningBaruReportResource\Pages;

use Filament\Actions;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PembukaanRekeningExport;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\PembukaanRekeningBaruReportResource;

class ListPembukaanRekeningBaruReports extends ListRecords
{
    protected static string $resource = PembukaanRekeningBaruReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('exportfpembukaanrekening')
                ->label('Export')
                ->icon('heroicon-o-document-arrow-down')
                ->color('primary')
                ->action(function () {
                    $pembukaanrekening = $this->getFilteredTableQuery();
                    $this->applySortingToTableQuery($pembukaanrekening);

                    $fileName = 'report_pembukaan_rekening_' . date('Ymd_His') . '.xlsx';

                    return Excel::download(new PembukaanRekeningExport($pembukaanrekening), $fileName);
                }),
        ];
    }
}
