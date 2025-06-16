<?php

namespace App\Filament\Resources\DapemNedMasterResource\Pages;

use Filament\Actions;
use League\Csv\Reader;
use League\Csv\Statement;
use Filament\Actions\Action;
use App\Models\DapemNedMaster;
use Illuminate\Support\Facades\Storage;
use Filament\Notifications\Notification;
use Filament\Forms\Components\FileUpload;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\DapemNedMasterResource;

class ListDapemNedMasters extends ListRecords
{
    protected static string $resource = DapemNedMasterResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Action::make('importDel')
                ->label('Import DEL File')
                ->icon('heroicon-o-arrow-up-tray')
                ->form([
                    FileUpload::make('del_file')
                        ->label('File DEL')
                        ->required()
                        ->acceptedFileTypes([])
                        ->preserveFilenames()
                        ->disk('local')
                        ->directory('private/uploads'),
                ])
                ->action(function (array $data) {
                    $originalPath = $data['del_file'];
                    $originalFullPath = Storage::disk('local')->path($originalPath);

                    $newPath = preg_replace('/\.del$/', '.csv', $originalPath);
                    $newFullPath = Storage::disk('local')->path($newPath);
                    Storage::disk('local')->move($originalPath, $newPath);

                    if (!file_exists($newFullPath)) {
                        Notification::make()
                            ->title('File tidak ditemukan setelah rename.')
                            ->danger()
                            ->send();
                        return;
                    }

                    $csv = Reader::createFromPath($newFullPath, 'r');
                    $csv->setDelimiter(';');

                    $headers = [
                        'notas',
                        'nama_nasabah',
                        'kantor_bayar',
                        'jiwa',
                        'jenis',
                        'nominal_dapem',
                        'rek_replace',
                        'bulan_dapem',
                        'code1',
                        'code2',
                        'code3',
                        'code4',
                    ];

                    $stmt = (new Statement());
                    $records = $stmt->process($csv)->getRecords($headers);

                    foreach ($records as $record) {
                        $cleaned = array_map(function ($value) {
                            return $value !== null
                                ? preg_replace('/\s+/', ' ', trim($value))
                                : null;
                        }, $record);

                        DapemNedMaster::create([
                            'notas' => $cleaned['notas'] ?? null,
                            'nama_nasabah' => $cleaned['nama_nasabah'] ?? null,
                            'kantor_bayar' => $cleaned['kantor_bayar'] ?? null,
                            'jiwa' => $cleaned['jiwa'] ?? null,
                            'jenis' => $cleaned['jenis'] ?? null,
                            'nominal_dapem' => $cleaned['nominal_dapem'] ?? null,
                            'rek_replace' => $cleaned['rek_replace'] ?? null,
                            'bulan_dapem' => $cleaned['bulan_dapem'] ?? null,
                            'code1' => $cleaned['code1'] ?? null,
                            'code2' => $cleaned['code2'] ?? null,
                            'code3' => $cleaned['code3'] ?? null,
                            'code4' => $cleaned['code4'] ?? null,
                        ]);
                    }

                    Notification::make()
                        ->title('Import Berhasil!')
                        ->success()
                        ->send();
                }),
        ];
    }
}