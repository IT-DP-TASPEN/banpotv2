<?php

namespace App\Filament\Resources\FlaggingMutasiTifReportResource\Pages;

use Filament\Actions;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\FlaggingMutasiTifExport;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\FlaggingMutasiTifReportResource;

class ListFlaggingMutasiTifReports extends ListRecords
{
    protected static string $resource = FlaggingMutasiTifReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('exportflaggingmutasitif')
                ->label('Export')
                ->icon('heroicon-o-document-arrow-down')
                ->color('primary')
                ->action(function () {
                    $flaggingmutasitif = $this->getFilteredTableQuery();
                    $this->applySortingToTableQuery($flaggingmutasitif);

                    $fileName = 'report_flagging_mutasi_tif_' . date('Ymd_His') . '.xlsx';

                    return Excel::download(new FlaggingMutasiTifExport($flaggingmutasitif), $fileName);
                }),
        ];
    }
}
