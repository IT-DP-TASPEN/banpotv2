<?php

namespace App\Filament\Resources\PermintaanCheckingReportResource\Pages;

use Filament\Actions;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PermintaanCheckingExport;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\PermintaanCheckingReportResource;

class ListPermintaanCheckingReports extends ListRecords
{
    protected static string $resource = PermintaanCheckingReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('exportpermintaancheckinb')
                ->label('Export')
                ->icon('heroicon-o-document-arrow-down')
                ->color('primary')
                ->action(function () {
                    $checking = $this->getFilteredTableQuery();
                    $this->applySortingToTableQuery($checking);

                    $fileName = 'report_permintaan_checking_' . date('Ymd_His') . '.xlsx';

                    return Excel::download(new PermintaanCheckingExport($checking), $fileName);
                }),
        ];
    }
}
