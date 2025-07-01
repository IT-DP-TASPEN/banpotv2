<?php

namespace App\Filament\Resources\PermintaaanFlaggingTifReportResource\Pages;

use Filament\Actions;
use App\Exports\FlaggingTifExport;
use Maatwebsite\Excel\Facades\Excel;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\PermintaaanFlaggingTifReportResource;

class ListPermintaaanFlaggingTifReports extends ListRecords
{
    protected static string $resource = PermintaaanFlaggingTifReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('exportflaggingtif')
                ->label('Export')
                ->icon('heroicon-o-document-arrow-down')
                ->color('primary')
                ->action(function () {
                    $flaggingtif = $this->getFilteredTableQuery();
                    $this->applySortingToTableQuery($flaggingtif);

                    $fileName = 'report_flagging_tif_' . date('Ymd_His') . '.xlsx';

                    return Excel::download(new FlaggingTifExport($flaggingtif), $fileName);
                }),
        ];
    }
}
