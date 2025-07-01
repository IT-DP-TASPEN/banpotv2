<?php

namespace App\Filament\Resources\OpenFlaggingTifReportResource\Pages;

use Filament\Actions;
use App\Exports\OpenFlaggingExport;
use Maatwebsite\Excel\Facades\Excel;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\OpenFlaggingTifReportResource;

class ListOpenFlaggingTifReports extends ListRecords
{
    protected static string $resource = OpenFlaggingTifReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('exportopenflagging')
                ->label('Export')
                ->icon('heroicon-o-document-arrow-down')
                ->color('primary')
                ->action(function () {
                    $openflagging = $this->getFilteredTableQuery();
                    $this->applySortingToTableQuery($openflagging);

                    $fileName = 'report_open_flagging_tif_' . date('Ymd_His') . '.xlsx';

                    return Excel::download(new OpenFlaggingExport($openflagging), $fileName);
                }),
        ];
    }
}
