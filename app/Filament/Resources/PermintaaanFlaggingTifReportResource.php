<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use App\Models\PermintaaanFlaggingTif;
use Illuminate\Database\Eloquent\Builder;
use App\Models\PermintaaanFlaggingTifReport;
use Filament\Tables\Columns\Summarizers\Summarizer;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\PermintaaanFlaggingTifReportResource\Pages;
use App\Filament\Resources\PermintaaanFlaggingTifReportResource\RelationManagers;

class PermintaaanFlaggingTifReportResource extends Resource
{
    protected static ?string $model = PermintaaanFlaggingTifReport::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-arrow-down';
    protected static ?string $navigationGroup = 'Report';
    protected static ?string $navigationLabel = 'Report Flagging TIF';

    protected static ?int $navigationSort = 24;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('permintaan_id')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->default(function () {
                        // Ambil ID transaksi terakhir
                        $latest = PermintaaanFlaggingTif::orderBy('id', 'desc')->first();

                        // Generate nomor urut
                        $sequence = $latest ?
                            (int) str_replace('FT', '', $latest->permintaan_id) + 1 :
                            1;

                        return 'FT' . str_pad($sequence, 5, '0', STR_PAD_LEFT);
                    })
                    ->disabled()
                    ->dehydrated()
                    ->columnSpanFull()
                    ->extraInputAttributes(['style' => 'text-align: center;']),
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
                Tables\Columns\TextColumn::make('jenis_pensiun'),
                Tables\Columns\TextColumn::make('jenis_flagging'),
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
                Tables\Columns\TextColumn::make('sp_deb_flagging')
                    ->searchable(),
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
                            ? 'Rp ' . number_format($value, 0, ',', '.')
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
                            ->formatStateUsing(fn($state) => 'Rp ' . number_format($state, 0, ',', '.'))
                            ->numeric()
                    ]),
                Tables\Columns\TextColumn::make('status_permintaan'),
                Tables\Columns\TextColumn::make('bukti_hasil')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_by')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('updated_by')
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
                Tables\Filters\TrashedFilter::make(),
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
            'index' => Pages\ListPermintaaanFlaggingTifReports::route('/'),
            // 'create' => Pages\CreatePermintaaanFlaggingTifReport::route('/create'),
            'view' => Pages\ViewPermintaaanFlaggingTifReport::route('/{record}'),
            'edit' => Pages\EditPermintaaanFlaggingTifReport::route('/{record}/edit'),
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
}