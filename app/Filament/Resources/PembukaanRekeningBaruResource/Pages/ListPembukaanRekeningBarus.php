<?php

namespace App\Filament\Resources\PembukaanRekeningBaruResource\Pages;

use App\Exports\PembukaanRekeningBaruNasabahOrangExport;
use Filament\Actions;
use Filament\Actions\Action;
use Maatwebsite\Excel\Facades\Excel;
use Filament\Resources\Pages\ListRecords;
use App\Exports\PembukaanRekeningBaruNasabahMasterExport;
use App\Exports\PembukaanRekeningBaruTabunganMasterExport;
use App\Exports\PembukaanRekeningBaruTabunganPelengkapExport;
use App\Filament\Resources\PembukaanRekeningBaruResource;
use Filament\Actions\ActionGroup;

class ListPembukaanRekeningBarus extends ListRecords
{
    protected static string $resource = PembukaanRekeningBaruResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            ActionGroup::make([
                Action::make('exportnasabahmaster')
                    ->label('Export Nasabah Master')
                    ->icon('heroicon-m-arrow-down-tray')
                    ->color('success')
                    ->action(fn() => Excel::download(new PembukaanRekeningBaruNasabahMasterExport, 'nasabah_master.xlsx')),

                Action::make('exportnasabahorang')
                    ->label('Export Nasabah Orang')
                    ->icon('heroicon-m-arrow-down-tray')
                    ->color('success')
                    ->action(fn() => Excel::download(new PembukaanRekeningBaruNasabahOrangExport, 'nasabah_orang.xlsx')),
                Action::make('exporttabunganmaster')
                    ->label('Export Tabungan Master')
                    ->icon('heroicon-m-arrow-down-tray')
                    ->color('success')
                    ->action(fn() => Excel::download(new PembukaanRekeningBaruTabunganMasterExport, 'tabungan_master.xlsx')),
                Action::make('exporttabunganpelengkap')
                    ->label('Export Tabungan Pelengkap')
                    ->icon('heroicon-m-arrow-down-tray')
                    ->color('success')
                    ->action(fn() => Excel::download(new PembukaanRekeningBaruTabunganPelengkapExport, 'tabungan_pelengkap.xlsx')),
            ])
                ->label('Format Bulk pembuatan rekening')
                ->button()
                ->visible(fn() => auth()->user()->isAdmin() || auth()->user()->isSuperAdmin() || auth()->user()->isStaffBankDPTaspen() || auth()->user()->isApprovalBankDPTaspen()),
        ];
    }
}
