<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use App\Models\PembukaanRekeningBaru;
use Filament\Support\Enums\ActionSize;
use Illuminate\Database\Eloquent\Builder;
use App\Models\PembukaanRekeningBaruApprovalMitra;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\PembukaanRekeningBaruApprovalMitraResource\Pages;
use App\Filament\Resources\PembukaanRekeningBaruApprovalMitraResource\RelationManagers;

class PembukaanRekeningBaruApprovalMitraResource extends Resource
{
    protected static ?string $model = PembukaanRekeningBaruApprovalMitra::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-check';
    protected static ?string $navigationLabel = 'Approval New Saving Account';
    protected static ?string $navigationGroup = 'Saving Account';
    protected static ?int $navigationSort = 13;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('permintaan_id')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->default(function () {
                        // Ambil ID transaksi terakhir
                        $latest = PembukaanRekeningBaru::orderBy('id', 'desc')->first();

                        // Generate nomor urut
                        $sequence = $latest ?
                            (int) str_replace('P', '', $latest->permintaan_id) + 1 :
                            1;

                        return 'P' . str_pad($sequence, 5, '0', STR_PAD_LEFT);
                    })
                    ->disabled()
                    ->dehydrated()
                    ->columnSpanFull()
                    ->extraInputAttributes(['style' => 'text-align: center;']),
                Forms\Components\TextInput::make('wilayah')
                    ->required(),
                Forms\Components\Select::make('jenis_akun')
                    ->options([
                        '1' => 'Perorangan',
                        '2' => 'Badan'
                    ])
                    ->default('1')
                    ->live()
                    ->reactive()
                    ->afterStateUpdated(function (callable $set) {
                        // Reset the 'nik' field when 'jenis_akun' changes
                        $set('nik', null);
                    }),
                Forms\Components\TextInput::make('nama_nasabah')
                    ->label(fn(Forms\Get $get) => $get('jenis_akun') === '1' ? 'Nama Nasabah' : 'Nama Instansi')
                    ->required(),

                Forms\Components\TextInput::make('notas')
                    ->maxLength(255)
                    ->required()
                    ->visible(fn(Forms\Get $get) => $get('jenis_akun') === '1'),
                Forms\Components\TextInput::make('nik')
                    ->label(fn(Forms\Get $get) => $get('jenis_akun') === '1' ? 'NIK' : 'Nomor NPWP')
                    ->numeric()
                    ->rules(fn(Forms\Get $get) => $get('jenis_akun') === '1' ? ['digits:16'] : [])
                    ->live(),
                Forms\Components\Select::make('pendidikan')
                    ->options([
                        'None' => 'None',
                        'TK' => 'TK',
                        'SD' => 'SD',
                        'SMP' => 'SMP',
                        'SMA' => 'SMA',
                        'SMK' => 'SMK',
                        'D1' => 'D1',
                        'D2' => 'D2',
                        'D3' => 'D3',
                        'D4' => 'D4',
                        'S1' => 'S1',
                        'S2' => 'S2',
                        'S3' => 'S3',
                    ])
                    ->visible(fn(Forms\Get $get) => $get('jenis_akun') === '1'),
                Forms\Components\TextInput::make('tempat_lahir')
                    ->maxLength(20)
                    ->visible(fn(Forms\Get $get) => $get('jenis_akun') === '1'),
                Forms\Components\DatePicker::make('tanggal_lahir')
                    ->default(now())
                    ->visible(fn(Forms\Get $get) => $get('jenis_akun') === '1'),
                Forms\Components\TextInput::make('no_handphone')
                    ->label(fn(Forms\Get $get) => $get('jenis_akun') === '1' ? 'Nomor Handphone' : 'Nomor Kantor')
                    ->tel()
                    ->maxLength(255)
                    ->required(),
                Forms\Components\TextInput::make('kontak_darurat')
                    ->label(fn(Forms\Get $get) => $get('jenis_akun') === '1' ? 'Kontak Darurat' : 'Nomor Alternatif')
                    ->maxLength(255)
                    ->required(),
                Forms\Components\Textarea::make('alamat')
                    ->columnSpanFull()
                    ->required(),
                Forms\Components\TextInput::make('nama_ibu_kandung')
                    ->maxLength(255)
                    ->visible(fn(Forms\Get $get) => $get('jenis_akun') === '1')
                    ->required(),
                Forms\Components\Select::make('status_nikah')
                    ->options([
                        '1' => 'Belum menikah',
                        '2' => 'Sudah menikah',
                        '3' => 'Cerai hidup',
                        '4' => 'Cerai mati'
                    ])
                    ->live()
                    ->visible(fn(Forms\Get $get) => $get('jenis_akun') === '1'),
                Forms\Components\TextInput::make('nama_pasangan')
                    ->maxLength(255)
                    ->visible(fn(Forms\Get $get) => $get('status_nikah') === '2'),
                Forms\Components\TextInput::make('nik_pasangan')
                    ->maxLength(16)
                    ->minLength(16)
                    ->visible(fn(Forms\Get $get) => $get('status_nikah') === '2')
                    ->numeric()
                    ->rules(['digits:16']),


                Forms\Components\FileUpload::make('form_buka_tab')
                    ->label(fn(Forms\Get $get) => $get('jenis_akun') === '1' ? 'Scan KTP KK Formulir Pembukaan Rekening' : 'Scan NPWP')
                    ->columnSpanFull()
                    ->previewable(true)
                    ->openable()
                    ->downloadable(),

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

                        if (auth()->user()->isApprovalMitraCabang()) {
                            $options['3'] = 'Approved by Mitra';
                            $options['4'] = 'Rejected by Mitra';
                            $options['5'] = 'Canceled by Mitra';
                        }


                        return $options;
                    })
                    ->default('1')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('rek_tabungan')
                    ->maxLength(255)
                    ->visible(fn() => auth()->user()->isAdmin()),
                Forms\Components\TextInput::make('created_by')
                    ->required()
                    ->hidden()
                    ->numeric(),
                Forms\Components\TextInput::make('updated_by')
                    ->hidden()
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('permintaan_id')
                    ->searchable(),
                Tables\Columns\TextColumn::make('jenis_akun')
                    ->hidden(),
                Tables\Columns\TextColumn::make('notas')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nik')
                    ->searchable(),
                Tables\Columns\TextColumn::make('tanggal_lahir')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('pendidikan')
                    ->searchable(),
                Tables\Columns\TextColumn::make('no_handphone')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status_nikah')
                    ->formatStateUsing(fn($state) => match ($state) {
                        '1' => 'Belum menikah',
                        '2' => 'Sudah menikah',
                        '3' => 'Cerai hidup',
                        '4' => 'Cerai mati',
                        default => 'Tidak diketahui',
                    })
                    ->searchable(),
                Tables\Columns\TextColumn::make('nik_pasangan')
                    ->hidden(),
                Tables\Columns\TextColumn::make('kontak_darurat')
                    ->searchable(),
                Tables\Columns\TextColumn::make('form_buka_tab')
                    ->searchable(),
                Tables\Columns\TextColumn::make('rek_tabungan')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status_permintaan')
                    ->formatStateUsing(fn($state) => match ($state) {
                        '1' => 'requested',
                        '2' => 'on proses',
                        '3' => 'cancelled',
                        default => 'unknown',
                    })
                    ->searchable()
                    ->badge(),

                Tables\Columns\TextColumn::make('created_by')
                    ->numeric()
                    ->hidden()
                    ->sortable(),
                Tables\Columns\TextColumn::make('updated_by')
                    ->searchable()
                    ->hidden(),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->dateTime()
                    ->hidden()
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
                Tables\Actions\Action::make('print')
                    ->label('Bukti Pembukaan Rekening')
                    ->icon('heroicon-o-printer')
                    ->color('success')
                    ->visible(fn($record) => !empty($record->rek_tabungan))
                    ->size(ActionSize::Small)
                    ->url(fn($record) => route('form_tab', $record))
                    ->openUrlInNewTab()
                    ->tooltip('Bukti Pembukaan Rekening'),
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
            'index' => Pages\ListPembukaanRekeningBaruApprovalMitras::route('/'),
            // 'create' => Pages\CreatePembukaanRekeningBaruApprovalMitra::route('/create'),
            'view' => Pages\ViewPembukaanRekeningBaruApprovalMitra::route('/{record}'),
            'edit' => Pages\EditPembukaanRekeningBaruApprovalMitra::route('/{record}/edit'),
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
        return auth()->user()->isAdmin() || auth()->user()->isSuperAdmin() || auth()->user()->isApprovalMitraCabang();
    }
}