<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use App\Models\PermintaanMutasiTos;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Columns\Summarizers\Summarizer;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\PermintaanMutasiTosResource\Pages;
use App\Filament\Resources\PermintaanMutasiTosResource\RelationManagers;

class PermintaanMutasiTosResource extends Resource
{
    protected static ?string $model = PermintaanMutasiTos::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-arrow-up';
    protected static ?string $navigationGroup = 'Request Flagging and Mutasi';
    protected static ?string $navigationLabel = 'Request Mutasi TOS';
    protected static ?int $navigationSort = 20;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Forms\Components\TextInput::make('permintaan_id')
                //     ->required()
                //     ->unique(ignoreRecord: true)
                //     ->default(function () {
                //         // Ambil ID transaksi terakhir
                //         $latest = PermintaanMutasiTos::orderBy('id', 'desc')->first();

                //         // Generate nomor urut
                //         $sequence = $latest ?
                //             (int) str_replace('MT', '', $latest->permintaan_id) + 1 :
                //             1;

                //         return 'MT' . str_pad($sequence, 5, '0', STR_PAD_LEFT);
                //     })
                //     ->disabled()
                //     ->dehydrated()
                //     ->extraInputAttributes(['style' => 'text-align: center;']),
                Forms\Components\TextInput::make('wilayah')
                    ->required(),
                Forms\Components\TextInput::make('nama_nasabah'),
                Forms\Components\TextInput::make('notas'),
                Forms\Components\TextInput::make('nik')
                    ->maxLength(255),
                Forms\Components\TextInput::make('tempat_lahir'),
                Forms\Components\DatePicker::make('tanggal_lahir'),
                Forms\Components\TextInput::make('no_handphone')
                    ->tel()
                    ->maxLength(255),
                Forms\Components\Textarea::make('alamat')
                    ->columnSpanFull(),
                Forms\Components\FileUpload::make('ktp')
                    ->columnSpanFull(),
                Forms\Components\FileUpload::make('form_sp3r')
                    ->columnSpanFull(),
                Forms\Components\FileUpload::make('sk_pensiun')
                    ->columnSpanFull(),
                Forms\Components\FileUpload::make('foto_tab')
                    ->columnSpanFull(),
                Forms\Components\FileUpload::make('lampiran_persyaratan')
                    ->columnSpanFull(),
                Forms\Components\FileUpload::make('bukti_hasil')
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('keterangan')
                    ->columnSpanFull(),
                Forms\Components\Select::make('status_permintaan')
                    ->options(function () {
                        $options = [
                            '1' => 'Request',

                        ];
                        // Add admin-only option if user is admin
                        if (auth()->user()->isAdmin() || auth()->user()->isSuperAdmin()) { // Adjust this condition as needed
                            $options['2'] = 'Checked by Mitra';
                            $options['3'] = 'Approved by Mitra';
                            $options['4'] = 'Rejected by Mitra';
                            $options['5'] = 'Canceled by Mitra';
                            $options['6'] = 'Checked by Bank DP Taspen';
                            $options['7'] = 'Approved by Bank DP Taspen';
                            $options['8'] = 'Rejected by Bank DP Taspen';
                            $options['9'] = 'On Process';
                            $options['10'] = 'Success';
                            $options['11'] = 'Failed';
                        }

                        if (auth()->user()->isStaffBankDPTaspen()) {

                            $options['6'] = 'Checked by Bank DP Taspen';
                            $options['9'] = 'On Process';
                            $options['10'] = 'Success';
                            $options['11'] = 'Failed';
                        }

                        if (auth()->user()->isApprovalBankDPTaspen()) {
                            $options['7'] = 'Approved by Bank DP Taspen';
                            $options['8'] = 'Rejected by Bank DP Taspen';
                        }

                        if (auth()->user()->isApprovalMitraPusat()) {
                            $options['3'] = 'Approved by Mitra';
                            $options['4'] = 'Rejected by Mitra';
                            $options['5'] = 'Canceled by Mitra';
                        }


                        return $options;
                    })
                    ->default('1')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('permintaan_id')
                    ->searchable(),
                Tables\Columns\TextColumn::make('notas')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nik')
                    ->searchable(),
                Tables\Columns\TextColumn::make('tanggal_lahir')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('no_handphone')
                    ->searchable(),
                Tables\Columns\TextColumn::make('biaya_flagging_mutasi_tos')
                    ->sortable()
                    ->label('Biaya Mutasi Tos')
                    ->getStateUsing(fn($record) => $record->mitraMaster->biaya_flagging_mutasi_tos ?? 0)
                    ->formatStateUsing(fn($state) =>  number_format($state, 0, ',', '.'))
                    ->summarize([
                        Summarizer::make()
                            ->label('Total')
                            ->using(
                                fn() =>
                                PermintaanMutasiTos::query()
                                    ->with('mitraMaster')
                                    ->get()
                                    ->sum(fn($record) => $record->mitraMaster->biaya_flagging_mutasi_tos ?? 0)
                            )
                            ->formatStateUsing(fn($state) =>   number_format($state, 0, ',', '.'))
                            ->numeric()
                    ]),
                Tables\Columns\TextColumn::make('biaya_checking')
                    ->hidden()
                    ->sortable()
                    ->label('BiayaChecking')
                    ->getStateUsing(fn($record) => $record->mitraMaster->biaya_checking ?? 0)
                    ->formatStateUsing(fn($state) =>  number_format($state, 0, ',', '.'))
                    ->summarize([
                        Summarizer::make()
                            ->label('Total')
                            ->using(
                                fn() =>
                                PermintaanMutasiTos::query()
                                    ->with('mitraMaster')
                                    ->get()
                                    ->sum(fn($record) => $record->mitraMaster->biaya_checking ?? 0)
                            )
                            ->formatStateUsing(fn($state) =>   number_format($state, 0, ',', '.'))
                            ->numeric()
                    ]),
                Tables\Columns\IconColumn::make('ktp')
                    ->label('Foto KTP')
                    ->icon('heroicon-o-document-text')
                    ->url(fn($record) => Storage::url($record->ktp))
                    ->openUrlInNewTab()
                    ->tooltip('Foto KTP')
                    ->alignCenter(),
                Tables\Columns\IconColumn::make('form_sp3r')
                    ->label('Form SP3R')
                    ->icon('heroicon-o-document-text')
                    ->url(fn($record) => Storage::url($record->form_sp3r))
                    ->openUrlInNewTab()
                    ->tooltip('Form SP3R')
                    ->alignCenter(),
                Tables\Columns\IconColumn::make('sk_pensiun')
                    ->label('Surat Keterangan Pensiun')
                    ->icon('heroicon-o-document-text')
                    ->url(fn($record) => Storage::url($record->sk_pensiun))
                    ->openUrlInNewTab()
                    ->tooltip('Surat Keterangan Pensiun')
                    ->alignCenter(),
                Tables\Columns\IconColumn::make('foto_tab')
                    ->label('Foto Tabungan')
                    ->icon('heroicon-o-document-text')
                    ->url(fn($record) => Storage::url($record->foto_tab))
                    ->openUrlInNewTab()
                    ->tooltip('Lampiran Persyaratan')
                    ->alignCenter(),
                Tables\Columns\IconColumn::make('lampiran_persyaratan')
                    ->label('Foto Tabungan')
                    ->icon('heroicon-o-document-text')
                    ->url(fn($record) => Storage::url($record->lampiran_persyaratan))
                    ->openUrlInNewTab()
                    ->tooltip('Lampiran Persyaratan')
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('status_permintaan')
                    ->label('Status Permintaan')
                    ->formatStateUsing(function ($state) {
                        $statuses = [
                            '1' => 'Request',
                            '2' => 'Checked by Mitra',
                            '3' => 'Approved by Mitra',
                            '4' => 'Rejected by Mitra',
                            '5' => 'Canceled by Mitra',
                            '6' => 'Checked by Bank DP Taspen',
                            '7' => 'Approved by Bank DP Taspen',
                            '8' => 'Rejected by Bank DP Taspen',
                            '9' => 'On Process',
                            '10' => 'Success',
                            '11' => 'Failed',
                        ];

                        return $statuses[$state] ?? '-';
                    })
                    ->badge()
                    ->color(function ($state) {
                        return match ($state) {
                            '1' => 'gray',
                            '2', '6' => 'warning',
                            '3', '7', '10' => 'success',
                            '4', '5', '8', '11' => 'danger',
                            '9' => 'info',
                            default => 'secondary',
                        };
                    }),
                Tables\Columns\IconColumn::make('bukti_hasil')
                    ->label('Bukti Hasil')
                    ->icon('heroicon-o-document-text')
                    ->url(fn($record) => Storage::url($record->bukti_hasil))
                    ->openUrlInNewTab()
                    ->tooltip('Bukti Hasil')
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('keterangan')
                    ->searchable(),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')->label('Created From'),
                        Forms\Components\DatePicker::make('created_until')->label('Created Until'),
                    ])
                    ->query(function (Builder $query, array $data) {
                        return $query
                            ->when($data['created_from'], fn($query, $date) => $query->whereDate('created_at', '>=', $date))
                            ->when($data['created_until'], fn($query, $date) => $query->whereDate('created_at', '<=', $date));
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPermintaanMutasiTos::route('/'),
            'create' => Pages\CreatePermintaanMutasiTos::route('/create'),
            'view' => Pages\ViewPermintaanMutasiTos::route('/{record}'),
            'edit' => Pages\EditPermintaanMutasiTos::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);

        $user = auth()->user();

        if ($user->isAdmin() || $user->isSuperAdmin()) {
            return $query;
        }

        // For approval mitra pusat (role 4), show all data from their mitra's branches
        if ($user->roles == '4') {
            return $query->whereHas('user', function ($q) use ($user) {
                $q->where('mitra_id', $user->mitra_id);
            });
        }

        // For other roles (approval cabang/staff), show only their branch data
        return $query->where('created_by', auth()->id())
            ->orWhereHas('user', function ($q) use ($user) {
                $q->where('mitra_cabang_id', $user->mitra_cabang_id);
            });
    }

    public static function canViewAny(): bool
    {
        return auth()->user()->isAdmin() || auth()->user()->isSuperAdmin() || auth()->user()->isStaffMitraCabang();
    }
}
