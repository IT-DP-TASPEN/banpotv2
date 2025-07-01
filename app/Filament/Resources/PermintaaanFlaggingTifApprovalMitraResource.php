<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use App\Models\PermintaaanFlaggingTif;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Builder;
use App\Models\PermintaaanFlaggingTifApprovalMitra;
use Filament\Tables\Columns\Summarizers\Summarizer;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\PermintaaanFlaggingTifApprovalMitraResource\Pages;
use App\Filament\Resources\PermintaaanFlaggingTifApprovalMitraResource\RelationManagers;

class PermintaaanFlaggingTifApprovalMitraResource extends Resource
{
    protected static ?string $model = PermintaaanFlaggingTifApprovalMitra::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-check';
    protected static ?string $navigationGroup = 'Approval Flagging and Mutasi';

    protected static ?string $navigationLabel = 'Approval Flagging TIF';
    protected static ?int $navigationSort = 17;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Forms\Components\TextInput::make('permintaan_id')
                //     ->required()
                //     ->unique(ignoreRecord: true)
                //     ->default(function () {
                //         // Ambil ID transaksi terakhir
                //         $latest = PermintaaanFlaggingTif::orderBy('id', 'desc')->first();

                //         // Generate nomor urut
                //         $sequence = $latest ?
                //             (int) str_replace('FT', '', $latest->permintaan_id) + 1 :
                //             1;

                //         return 'FT' . str_pad($sequence, 5, '0', STR_PAD_LEFT);
                //     })
                //     ->disabled()
                //     ->dehydrated()
                //     ->columnSpanFull()
                //     ->extraInputAttributes(['style' => 'text-align: center;']),
                Forms\Components\TextInput::make('wilayah')
                    ->required(),
                Forms\Components\Select::make('jenis_pensiun')
                    ->options([
                        '1' => 'Pensiun',
                        '2' => 'Aktif',
                    ])
                    ->required(),
                Forms\Components\Select::make('jenis_flagging')
                    ->options([
                        '1' => 'Permintaan Flagging Pensiun ( TIF )',
                        '2' => 'Permintaan Flagging THT ( TIF )',
                        '3' => 'Permintaan Flagging Prapen ( TIF )',
                        '4' => 'Permintaan Flagging Prapen THT ( TIF )',
                    ])
                    ->required(),
                Forms\Components\TextInput::make('nama_nasabah')
                    ->required(),
                Forms\Components\TextInput::make('notas')
                    ->maxLength(255),
                Forms\Components\TextInput::make('nik')
                    ->maxLength(255),
                Forms\Components\TextInput::make('tempat_lahir'),
                Forms\Components\DatePicker::make('tanggal_lahir'),
                Forms\Components\Textarea::make('alamat')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('no_handphone')
                    ->tel()
                    ->maxLength(255),
                Forms\Components\TextInput::make('rek_tabungan')
                    ->maxLength(255),
                Forms\Components\TextInput::make('rek_kredit')
                    ->maxLength(255),
                Forms\Components\DatePicker::make('tat_kredit'),
                Forms\Components\FileUpload::make('sp_deb_flagging')
                    ->label('Surat Pernyataan Debitur Flagging')
                    ->columnSpanFull(),
                Forms\Components\FileUpload::make('bukti_hasil')
                    ->label('Bukti Hasil')
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
                Tables\Columns\TextColumn::make('jenis_pensiun')
                    ->label('Jenis Pensiunan')
                    ->formatStateUsing(function ($state) {
                        $statuses = [
                            '1' => 'Pensiun',
                            '2' => 'Aktif',
                        ];

                        return $statuses[$state] ?? '-';
                    }),
                Tables\Columns\TextColumn::make('jenis_flagging')
                    ->label('Jenis Flagging')
                    ->formatStateUsing(function ($state) {
                        $statuses = [
                            '1' => 'Permintaan Flagging Pensiun ( TIF )',
                            '2' => 'Permintaan Flagging THT ( TIF )',
                            '3' => 'Permintaan Flagging Prapen ( TIF )',
                            '4' => 'Permintaan Flagging Prapen THT ( TIF )',
                        ];

                        return $statuses[$state] ?? '-';
                    }),
                Tables\Columns\TextColumn::make('notas')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nik')
                    ->searchable(),
                Tables\Columns\TextColumn::make('tanggal_lahir')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('no_handphone')
                    ->searchable(),
                Tables\Columns\TextColumn::make('rek_tabungan')
                    ->searchable(),
                Tables\Columns\TextColumn::make('rek_kredit')
                    ->searchable(),
                Tables\Columns\TextColumn::make('tat_kredit')
                    ->date()
                    ->sortable(),
                Tables\Columns\IconColumn::make('sp_deb_flagging')
                    ->label('Surat Pernyataan Debitur Flagging')
                    ->icon('heroicon-o-document-text')
                    ->url(fn($record) => Storage::url($record->sp_deb_flagging))
                    ->openUrlInNewTab()
                    ->tooltip('Surat Pernyataan Debitur Flagging')
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('biaya_flagging')
                    ->label('Biaya Flagging')
                    ->formatStateUsing(function ($record) {
                        if (!$record->relationLoaded('mitraMaster')) {
                            $record->load('mitraMaster');
                        }
                        $biayaMapping = [
                            '1' => 'biaya_flagging_pensiun',
                            '2' => 'biaya_flagging_tht',
                            '3' => 'biaya_flagging_prapen',
                            '4' => 'biaya_flagging_prapen_tht',
                        ];
                        $column = $biayaMapping[$record->jenis_flagging] ?? null;
                        $value = $column ? ($record->mitraMaster->{$column} ?? null) : null;
                        return $value !== null
                            ?  number_format($value, 0, ',', '.')
                            : '-';
                    })
                    ->default('-')
                    ->summarize([
                        Summarizer::make()
                            ->label('Total Pembayaran')
                            ->using(function () {
                                return PermintaaanFlaggingTif::query()
                                    ->with('mitraMaster')
                                    ->get()
                                    ->sum(function ($record) {
                                        if (!$record->relationLoaded('mitraMaster')) {
                                            $record->load('mitraMaster');
                                        }
                                        $biayaMapping = [
                                            '1' => 'biaya_flagging_pensiun',
                                            '2' => 'biaya_flagging_tht',
                                            '3' => 'biaya_flagging_prapen',
                                            '4' => 'biaya_flagging_prapen_tht',
                                        ];
                                        $column = $biayaMapping[$record->jenis_flagging] ?? null;
                                        return $column ? ($record->mitraMaster->{$column} ?? 0) : 0;
                                    });
                            })
                            ->formatStateUsing(fn($state) =>  number_format($state, 0, ',', '.'))
                            ->numeric()
                    ]),
                Tables\Columns\TextColumn::make('biaya_checking')
                    ->label('Fee Checking')
                    ->getStateUsing(fn($record) => $record->mitraMaster->biaya_checking ?? 0)
                    ->formatStateUsing(fn($state) =>  number_format($state, 0, ',', '.'))
                    ->summarize([
                        Summarizer::make()
                            ->label('Total')
                            ->using(
                                fn() =>
                                PermintaaanFlaggingTif::query()
                                    ->with('mitraMaster')
                                    ->get()
                                    ->sum(fn($record) => $record->mitraMaster->biaya_checking ?? 0)
                            )
                            ->formatStateUsing(fn($state) =>   number_format($state, 0, ',', '.'))
                            ->numeric()
                    ]),
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
                Tables\Columns\TextColumn::make('created_by')
                    ->numeric()
                    ->sortable()
                    ->hidden(),
                Tables\Columns\TextColumn::make('updated_by')
                    ->searchable()
                    ->hidden(),
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
                    })->indicateUsing(function (array $data): array {
                        $indicators = [];

                        if ($data['created_from'] ?? null) {
                            $indicators[] = 'From: ' . \Carbon\Carbon::parse($data['created_from'])->format('d M Y');
                        }

                        if ($data['created_until'] ?? null) {
                            $indicators[] = 'Until: ' . \Carbon\Carbon::parse($data['created_until'])->format('d M Y');
                        }

                        return $indicators;
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // Tables\Actions\DeleteBulkAction::make(),
                    // Tables\Actions\ForceDeleteBulkAction::make(),
                    // Tables\Actions\RestoreBulkAction::make(),
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
            'index' => Pages\ListPermintaaanFlaggingTifApprovalMitras::route('/'),
            // 'create' => Pages\CreatePermintaaanFlaggingTifApprovalMitra::route('/create'),
            'view' => Pages\ViewPermintaaanFlaggingTifApprovalMitra::route('/{record}'),
            'edit' => Pages\EditPermintaaanFlaggingTifApprovalMitra::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $user = auth()->user();
        return parent::getEloquentQuery()
            ->when(
                $user->roles == '7',
                fn($query) => $query
                    ->where('created_by', $user->id)
                    ->whereHas('creator', function ($q) use ($user) {
                        $q->where('mitra_id', $user->mitra_id)
                            ->where('mitra_cabang_id', $user->mitra_cabang_id);
                    })
            )
            ->when(
                $user->roles == '5',
                fn($query) => $query
                    ->whereHas('creator', function ($q) use ($user) {
                        $q->where('roles', '7')
                            ->where('mitra_id', $user->mitra_id)
                            ->where('mitra_cabang_id', $user->mitra_cabang_id);
                    })
            )
            ->when(
                !in_array($user->roles, ['5', '7']),
                fn($query) => $query // Roles lain tanpa filter
            );
    }

    public static function canViewAny(): bool
    {
        return auth()->user()->isAdmin() || auth()->user()->isSuperAdmin() || auth()->user()->isApprovalBankDPTaspen() || auth()->user()->isStaffBankDPTaspen() || auth()->user()->isApprovalMitraCabang();
    }
}
