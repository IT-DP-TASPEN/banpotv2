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
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\PembukaanRekeningBaruResource\Pages;
use App\Filament\Resources\PembukaanRekeningBaruResource\RelationManagers;

class PembukaanRekeningBaruResource extends Resource
{
    protected static ?string $model = PembukaanRekeningBaru::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Permintaan';
    protected static ?int $navigationSort = 1;

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
                Forms\Components\TextInput::make('nama_nasabah')
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
                    ]),
                Forms\Components\TextInput::make('tempat_lahir')
                    ->maxLength(20),
                Forms\Components\DatePicker::make('tanggal_lahir')
                    ->default(now()),


                Forms\Components\TextInput::make('no_handphone')
                    ->tel()
                    ->maxLength(255)
                    ->required(),
                Forms\Components\TextInput::make('kontak_darurat')
                    ->maxLength(255)
                    ->required(),
                Forms\Components\Textarea::make('alamat')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('nama_ibu_kandung')
                    ->maxLength(255)
                    ->required(),
                Forms\Components\Select::make('status_nikah')
                    ->options([
                        '1' => 'Belum menikah',
                        '2' => 'Sudah menikah',
                        '3' => 'Cerai hidup',
                        '4' => 'Cerai mati'
                    ])
                    ->live(),
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
                    ->columnSpanFull()
                    ->previewable(true)
                    ->openable()
                    ->downloadable(),

                Forms\Components\Textarea::make('keterangan')
                    ->columnSpanFull(),
                Forms\Components\Select::make('status_permintaan')
                    ->options([
                        '1' => 'requested',
                        '2' => 'on proses',
                        '3' => 'cancelled',
                    ])
                    ->default(1)
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
                Tables\Columns\TextColumn::make('status_nikah'),
                Tables\Columns\TextColumn::make('nik_pasangan')
                    ->searchable(),
                Tables\Columns\TextColumn::make('kontak_darurat')
                    ->searchable(),
                Tables\Columns\TextColumn::make('form_buka_tab')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status_permintaan'),
                Tables\Columns\TextColumn::make('rek_tabungan')
                    ->searchable(),

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
            'index' => Pages\ListPembukaanRekeningBarus::route('/'),
            'create' => Pages\CreatePembukaanRekeningBaru::route('/create'),
            'view' => Pages\ViewPembukaanRekeningBaru::route('/{record}'),
            'edit' => Pages\EditPembukaanRekeningBaru::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
