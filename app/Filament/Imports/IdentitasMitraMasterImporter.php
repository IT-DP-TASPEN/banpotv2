<?php

namespace App\Filament\Imports;

use App\Models\IdentitasMitraMaster;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class IdentitasMitraMasterImporter extends Importer
{
    protected static ?string $model = IdentitasMitraMaster::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('mitra_id')
                ->requiredMapping(),
            ImportColumn::make('notas')
                ->requiredMapping(),
            ImportColumn::make('nama_nasabah')
                ->requiredMapping(),
            ImportColumn::make('rek_tabungan')
                ->requiredMapping(),
        ];
    }

    public function resolveRecord(): ?IdentitasMitraMaster
    {
        // return IdentitasMitraMaster::firstOrNew([
        //     // Update existing records, matching them by `$this->data['column_name']`
        //     'email' => $this->data['email'],
        // ]);

        return new IdentitasMitraMaster();
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your identitas mitra master import has completed and ' . number_format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
}