<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Support\Collection;
use Filament\Support\Enums\ActionSize;
use Illuminate\Support\Facades\Storage;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use App\Models\PembukaanRekeningBaruNeedProses;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\PembukaanRekeningBaruNeedProsesResource\Pages;
use App\Filament\Resources\PembukaanRekeningBaruNeedProsesResource\RelationManagers;

class PembukaanRekeningBaruNeedProsesResource extends Resource
{
    protected static ?string $model = PembukaanRekeningBaruNeedProses::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-check';
    protected static ?string $navigationLabel = 'Need Proses New Saving Account';
    protected static ?string $navigationGroup = 'Saving Account';
    protected static ?int $navigationSort = 14;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Forms\Components\TextInput::make('permintaan_id')
                //     ->required()
                //     ->unique(ignoreRecord: true)
                //     ->default(function () {
                //         // Ambil ID transaksi terakhir
                //         $latest = PembukaanRekeningBaru::orderBy('id', 'desc')->first();

                //         // Generate nomor urut
                //         $sequence = $latest ?
                //             (int) str_replace('P', '', $latest->permintaan_id) + 1 :
                //             1;

                //         return 'P' . str_pad($sequence, 5, '0', STR_PAD_LEFT);
                //     })
                //     ->disabled()
                //     ->dehydrated()
                //     ->columnSpanFull()
                //     ->extraInputAttributes(['style' => 'text-align: center;']),
                Forms\Components\TextInput::make('wilayah')
                    ->required(),
                Forms\Components\Select::make('jenis_akun')
                    ->options([
                        'orang' => 'Perorangan',
                        'badan' => 'Badan'
                    ])
                    ->default('orang')
                    ->live()
                    ->reactive()
                    ->afterStateUpdated(function (callable $set) {
                        // Reset the 'nik' field when 'jenis_akun' changes
                        $set('nik', null);
                    }),
                Forms\Components\TextInput::make('nama_nasabah')
                    ->label(fn(Forms\Get $get) => $get('jenis_akun') === 'orang' ? 'Nama Nasabah' : 'Nama Instansi')
                    ->required(),

                Forms\Components\TextInput::make('notas')
                    ->maxLength(255)
                    ->required()
                    ->visible(fn(Forms\Get $get) => $get('jenis_akun') === 'orang'),
                Forms\Components\TextInput::make('nik')
                    ->label(fn(Forms\Get $get) => $get('jenis_akun') === 'orang' ? 'NIK' : 'Nomor NPWP')
                    ->numeric()
                    ->rules(fn(Forms\Get $get) => $get('jenis_akun') === 'orang' ? ['digits:16'] : [])
                    ->live()
                    ->required(),
                Forms\Components\Select::make('pendidikan')
                    ->options([
                        '0100' => 'Tanpa Gelar',
                        '0101' => 'Diploma I',
                        '0102' => 'Diploma II',
                        '0103' => 'Diploma III (D3)',
                        '0104' => 'Sarjana (S1)',
                        '0105' => 'Pasca Sarjana (S2)',
                        '0106' => 'Doktoral (S3)',
                        '0199' => 'Lainnya'
                    ])
                    ->visible(fn(Forms\Get $get) => $get('jenis_akun') === 'orang')
                    ->required(),
                Forms\Components\Select::make('sex')
                    ->label('Jenis Kelamin')
                    ->options([
                        '1' => 'Laki - Laki',
                        '2' => 'Perempuan',
                    ])->required()
                    ->visible(fn(Forms\Get $get) => $get('jenis_akun') === 'orang'),
                Forms\Components\Select::make('agama')
                    ->options([
                        '1' => 'Islam',
                        '2' => 'Kristen',
                        '3'  => 'Katolik',
                        '4'  => 'Hindu',
                        '5'  => 'Budha',
                        '6'  => 'Konghucu',
                        '7'  => 'Lainnya',
                    ])->required()
                    ->visible(fn(Forms\Get $get) => $get('jenis_akun') === 'orang'),

                Forms\Components\TextInput::make('tempat_lahir')
                    ->maxLength(20)
                    ->visible(fn(Forms\Get $get) => $get('jenis_akun') === 'orang')->required(),
                Forms\Components\DatePicker::make('tanggal_lahir')
                    ->default(now())
                    ->visible(fn(Forms\Get $get) => $get('jenis_akun') === 'orang')->required(),
                Forms\Components\TextInput::make('no_handphone')
                    ->label(fn(Forms\Get $get) => $get('jenis_akun') === 'orang' ? 'Nomor Handphone' : 'Nomor Kantor')
                    ->tel()
                    ->maxLength(255)
                    ->required(),
                Forms\Components\TextInput::make('kontak_darurat')
                    ->label(fn(Forms\Get $get) => $get('jenis_akun') === 'orang' ? 'Kontak Darurat' : 'Nomor Alternatif')
                    ->maxLength(255)
                    ->required(),
                Forms\Components\Select::make('dati2')
                    ->label('Kota/Kabupaten')
                    ->searchable()
                    ->options([
                        '0102' => 'Bekasi, Kab.',
                        '0103' => 'Purwakarta, Kab.',
                        '0106' => 'Karawang, Kab.',
                        '0108' => 'Bogor, Kab.',
                        '0109' => 'Sukabumi, Kab.',
                        '0110' => 'Cianjur, Kab.',
                        '0111' => 'Bandung, Kab.',
                        '0112' => 'Sumedang, Kab.',
                        '0113' => 'Tasikmalaya, Kab.',
                        '0114' => 'Garut, Kab.',
                        '0115' => 'Ciamis, Kab.',
                        '0116' => 'Cirebon, Kab.',
                        '0117' => 'Kuningan, Kab.',
                        '0118' => 'Indramayu, Kab.',
                        '0119' => 'Majalengka, Kab.',
                        '0121' => 'Subang, Kab.',
                        '0122' => 'Bandung Barat, Kab.',
                        '0123' => 'Pangandaran, Kab.',
                        '0180' => 'Banjar, Kota.',
                        '0188' => 'Prov. Jawa Barat, Kab./Kota Lainnya.',
                        '0191' => 'Bandung, Kota.',
                        '0192' => 'Bogor, Kota.',
                        '0193' => 'Sukabumi, Kota.',
                        '0194' => 'Cirebon, Kota.',
                        '0195' => 'Tasikmalaya, Kota.',
                        '0196' => 'Cimahi, Kota.',
                        '0197' => 'Depok, Kota.',
                        '0198' => 'Bekasi, Kota.',
                        '0201' => 'Lebak, Kab.',
                        '0202' => 'Pandeglang, Kab.',
                        '0203' => 'Serang, Kab.',
                        '0204' => 'Tangerang, Kab.',
                        '0288' => 'Prov. Banten, Kab./Kota Lainnya.',
                        '0291' => 'Cilegon, Kota.',
                        '0292' => 'Tangerang, Kota.',
                        '0293' => 'Serang, Kota.',
                        '0294' => 'Tanggerang Selatan',
                        '0391' => 'Jakarta Pusat, Wil. Kota',
                        '0392' => 'Jakarta Utara ., Wil. Kota',
                        '0393' => 'Jakarta Barat, Wil. Kota',
                        '0394' => 'Jakarta Selatan, Wil. Kota',
                        '0395' => 'Jakarta Timur, Wil. Kota',
                        '0396' => 'Kepulauan Seribu, Wilayah',
                        '0501' => 'Bantul, Kab.',
                        '0502' => 'Sleman, Kab.',
                        '0503' => 'Gunung Kidul, Kab.',
                        '0504' => 'Kulon Progo, Kab.',
                        '0588' => 'DI Yogyakarta, Kab./Kota Lainnya.',
                        '0591' => 'Yogyakarta, Kota.',
                        '0901' => 'Semarang, Kab.',
                        '0902' => 'Kendal, Kab.',
                        '0903' => 'Demak, Kab.',
                        '0904' => 'Grobogan, Kab.',
                        '0905' => 'Pekalongan, Kab.',
                        '0906' => 'Tegal, Kab.',
                        '0907' => 'Brebes, Kab.',
                        '0908' => 'Pati, Kab.',
                        '0909' => 'Kudus, Kab.',
                        '0910' => 'Pemalang, Kab.',
                        '0911' => 'Jepara, Kab.',
                        '0912' => 'Rembang, Kab.',
                        '0913' => 'Blora, Kab.',
                        '0914' => 'Banyumas, Kab.',
                        '0915' => 'Cilacap, Kab.',
                        '0916' => 'Purbalingga, Kab.',
                        '0917' => 'Banjarnegara, Kab.',
                        '0918' => 'Magelang, Kab.',
                        '0919' => 'Temanggung, Kab.',
                        '0920' => 'Wonosobo, Kab.',
                        '0921' => 'Purworejo, Kab.',
                        '0922' => 'Kebumen, Kab.',
                        '0923' => 'Klaten, Kab.',
                        '0924' => 'Boyolali, Kab.',
                        '0925' => 'Sragen, Kab.',
                        '0926' => 'Sukoharjo, Kab.',
                        '0927' => 'Karanganyar, Kab.',
                        '0928' => 'Wonogiri, Kab.',
                        '0929' => 'Batang, Kab.',
                        '0988' => 'Prov. Jawa Tengah, Kab./Kota Lainnya.',
                        '0991' => 'Semarang, Kota.',
                        '0992' => 'Salatiga, Kota.',
                        '0993' => 'Pekalongan, Kota.',
                        '0994' => 'Tegal, Kota.',
                        '0995' => 'Magelang, Kota.',
                        '0996' => 'Surakarta, Kota.',
                        '1201' => 'Gresik, Kab.',
                        '1202' => 'Sidoarjo, Kab.',
                        '1203' => 'Mojokerto, Kab.',
                        '1204' => 'Jombang, Kab.',
                        '1205' => 'Sampang, Kab.',
                        '1206' => 'Pamekasan, Kab.',
                        '1207' => 'Sumenep, Kab.',
                        '1208' => 'Bangkalan, Kab.',
                        '1209' => 'Bondowoso, Kab.',
                        '1211' => 'Banyuwangi, Kab.',
                        '1212' => 'Jember, Kab.',
                        '1213' => 'Malang, Kab.',
                        '1214' => 'Pasuruan, Kab.',
                        '1215' => 'Probolinggo, Kab.',
                        '1216' => 'Lumajang, Kab.',
                        '1217' => 'Kediri, Kab.',
                        '1218' => 'Nganjuk, Kab.',
                        '1219' => 'Tulungagung, Kab.',
                        '1220' => 'Trenggalek, Kab.',
                        '1221' => 'Blitar, Kab.',
                        '1222' => 'Madiun, Kab.',
                        '1223' => 'Ngawi, Kab.',
                        '1224' => 'Magetan, Kab.',
                        '1225' => 'Ponorogo, Kab.',
                        '1226' => 'Pacitan, Kab.',
                        '1227' => 'Bojonegoro, Kab.',
                        '1228' => 'Tuban, Kab.',
                        '1229' => 'Lamongan, Kab.',
                        '1230' => 'Situbondo, Kab.',
                        '1271' => 'Batu, Kota.',
                        '1288' => 'Prov. Jawa Timur, Kab./Kota Lainnya.',
                        '1291' => 'Surabaya, Kota.',
                        '1292' => 'Mojokerto, Kota.',
                        '1293' => 'Malang, Kota.',
                        '1294' => 'Pasuruan, Kota.',
                        '1295' => 'Probolinggo, Kota.',
                        '1296' => 'Blitar, Kota.',
                        '1297' => 'Kediri, Kota.',
                        '1298' => 'Madiun, Kota.',
                        '9999' => 'Di Luar Indonesia',
                    ])
                    ->required(),
                Forms\Components\TextInput::make('kecamatan')
                    ->required(),
                Forms\Components\TextInput::make('kelurahan')
                    ->required(),
                Forms\Components\TextInput::make('kode_pos')
                    ->required(),
                Forms\Components\Textarea::make('alamat')
                    ->label('Alamat Lengkap')
                    ->columnSpanFull()
                    ->required(),
                Forms\Components\TextInput::make('nama_ibu_kandung')
                    ->maxLength(255)
                    ->visible(fn(Forms\Get $get) => $get('jenis_akun') === 'orang')
                    ->required(),
                Forms\Components\TextInput::make('nama_ahli_waris')
                    ->maxLength(255)
                    ->visible(fn(Forms\Get $get) => $get('jenis_akun') === 'orang')
                    ->required(),
                Forms\Components\Select::make('hub_ahli_waris')
                    ->options([
                        '01' => 'SUAMI/ISTRI',
                        '02' => 'BAPAK/IBU KANDUNG',
                        '03' => 'BAPAK/IBU MERTUA',
                        '04' => 'BAPAK/IBU TIRI',
                        '05' => 'BAPAK/IBU ANGKAT',
                        '06' => 'KAKEK/NENEK',
                        '07' => 'PAMAN/BIBI',
                        '08' => 'SAUDARA KANDUNG',
                        '09' => 'SAUDARA IPAR',
                        '10' => 'SAUDARA TIRI',
                        '11' => 'SAUDARA ANGKAT',
                        '12' => 'SEPUPU KANDUNG',
                        '13' => 'SEPUPU IPAR',
                        '14' => 'ANAK KANDUNG',
                        '15' => 'ANAK TIRI',
                        '16' => 'ANAK ANGKAT',
                        '17' => 'KEPONAKAN KANDUNG',
                        '18' => 'KEPONAKAN IPAR',
                        '19' => 'CUCU',
                        '20' => 'KERABAT LAINNYA',
                        '99' => 'BUKAN KERABAT',
                    ])
                    ->required()
                    ->visible(fn(Forms\Get $get) => $get('jenis_akun') === 'orang'),
                Forms\Components\Select::make('status_nikah')
                    ->options([
                        '1' => 'Belum menikah',
                        '2' => 'Sudah menikah',
                        '3' => 'Cerai hidup',
                        '4' => 'Cerai mati'
                    ])
                    ->live()
                    ->visible(fn(Forms\Get $get) => $get('jenis_akun') === 'orang')
                    ->required(),
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
                    ->label('Dokumen Persyaratan')
                    ->helperText(fn(Forms\Get $get) => $get('jenis_akun') === 'orang' ? 'Scan KTP,KK,Formulir Pembukaan Rekening,Surat Perintah Kuasa Transfer' : 'Scan NPWP')
                    ->columnSpanFull()
                    ->previewable(true)
                    ->openable()
                    ->downloadable()
                    ->required(),
                Forms\Components\Textarea::make('keterangan')
                    ->columnSpanFull(),
                Forms\Components\Select::make('status_permintaan')
                    ->options(function () {
                        if (auth()->user()->isStaffMitraCabang()) { // Adjust this condition as needed
                            $options['1'] = 'Request';
                        }
                        // Add admin-only option if user is admin
                        if (auth()->user()->isAdmin() || auth()->user()->isSuperAdmin()) { // Adjust this condition as needed
                            $options['1'] = 'Request';
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
                Tables\Columns\TextColumn::make('notas')
                    ->label('Notas')
                    ->getStateUsing(
                        fn($record) => ($record->jenis_akun === 'badan' && is_null($record->notas))
                            ? 'Rekening Instansi'
                            : $record->notas
                    ),

                Tables\Columns\TextColumn::make('nik')
                    ->label('NIK / NPWP')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nama_nasabah')
                    ->label('Nama Nasabah / Instansi')
                    ->searchable(),
                Tables\Columns\TextColumn::make('tanggal_lahir')
                    ->date()
                    ->sortable()
                    ->getStateUsing(
                        fn($record) => ($record->jenis_akun === 'badan' && is_null($record->tanggal_lahir))
                            ? '9999-12-31'
                            : $record->tanggal_lahir
                    ),
                Tables\Columns\TextColumn::make('pendidikan')
                    ->searchable()
                    ->formatStateUsing(fn($state) => match ($state) {
                        '0100' => 'Tanpa Gelar',
                        '0101' => 'Diploma I',
                        '0102' => 'Diploma II',
                        '0103' => 'Diploma III (D3)',
                        '0104' => 'Sarjana (S1)',
                        '0105' => 'Pasca Sarjana (S2)',
                        '0106' => 'Doktoral (S3)',
                        '0199' => 'Lainnya',
                        default => 'Tidak diketahui',
                    })
                    ->getStateUsing(
                        fn($record) => ($record->jenis_akun === 'badan' && is_null($record->pendidikan))
                            ? 'Rekening Instansi'
                            : $record->pendidikan
                    ),
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
                    ->getStateUsing(
                        fn($record) => ($record->jenis_akun === 'badan' && is_null($record->status_nikah))
                            ? 'Rekening Instansi'
                            : $record->status_nikah
                    ),
                Tables\Columns\TextColumn::make('nik_pasangan')
                    ->hidden(),
                Tables\Columns\TextColumn::make('kontak_darurat')
                    ->searchable(),
                Tables\Columns\IconColumn::make('form_buka_tab')
                    ->label('Dokumen Persyaratan')
                    ->icon('heroicon-o-document-text')
                    ->url(fn($record) => Storage::url($record->form_buka_tab))
                    ->openUrlInNewTab()
                    ->tooltip('Lihat Scan KTP,KK,Formulir Pembukaan Rekening,Surat Perintah Kuasa Transfer / NPWP')
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('rek_tabungan')
                    ->searchable()
                    ->visible(fn($record) => !empty($record->status_permintaan) && in_array($record->status_permintaan, ['3', '6', '7', '9', '10'])),
                Tables\Columns\TextColumn::make('status_permintaan')
                    ->label('Status Banpot')
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
                Tables\Columns\TextColumn::make('keterangan')
                    ->badge()
                    ->color(function ($state) {
                        return match ($state) {
                            'Sudah di daftarkan' => 'danger',
                            default => 'secondary',
                        };
                    })
                    ->toggleable(isToggledHiddenByDefault: true),
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
                Tables\Actions\Action::make('print')
                    ->label('Bukti Pembukaan Rekening')
                    ->icon('heroicon-o-printer')
                    ->color('success')
                    ->visible(fn($record) => !empty($record->status_permintaan) && in_array($record->status_permintaan, ['3', '6', '7', '9', '10']))
                    ->size(ActionSize::Small)
                    ->url(fn($record) => route('form_tab', $record))
                    ->openUrlInNewTab()
                    ->tooltip('Bukti Pembukaan Rekening'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('updatestatuspermintaan')
                        ->label('Update Status Permintaan')
                        ->icon('heroicon-m-pencil-square')
                        ->form([
                            Forms\Components\Select::make('status_permintaan')
                                ->label('Select Status')
                                ->options(function () {
                                    $user = auth()->user();
                                    $options = [
                                        '1' => 'Request',
                                    ];

                                    if ($user->isAdmin() || $user->isSuperAdmin()) {
                                        $options += [
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
                                    }

                                    if ($user->isStaffBankDPTaspen()) {
                                        $options += [
                                            '6' => 'Checked by Bank DP Taspen',
                                            '9' => 'On Process',
                                            '10' => 'Success',
                                            '11' => 'Failed',
                                        ];
                                    }

                                    if ($user->isApprovalBankDPTaspen()) {
                                        $options += [
                                            '7' => 'Approved by Bank DP Taspen',
                                            '8' => 'Rejected by Bank DP Taspen',
                                        ];
                                    }

                                    if ($user->isApprovalMitraPusat()) {
                                        $options += [
                                            '3' => 'Approved by Mitra',
                                            '4' => 'Rejected by Mitra',
                                            '5' => 'Canceled by Mitra',
                                        ];
                                    }

                                    return $options;
                                })
                                ->required(),
                        ])
                        ->action(function (array $data, Collection $records) {
                            foreach ($records as $record) {
                                $record->update([
                                    'status_permintaan' => $data['status_permintaan'],
                                ]);
                            }

                            Notification::make()
                                ->title('Status updated successfully!')
                                ->success()
                                ->send();
                        })
                        ->requiresConfirmation()
                        ->modalHeading('Update Status')
                        ->modalSubheading('Select a new status for the selected records.')
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
            'index' => Pages\ListPembukaanRekeningBaruNeedProses::route('/'),
            'create' => Pages\CreatePembukaanRekeningBaruNeedProses::route('/create'),
            'view' => Pages\ViewPembukaanRekeningBaruNeedProses::route('/{record}'),
            'edit' => Pages\EditPembukaanRekeningBaruNeedProses::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ])
            ->whereIn('status_permintaan', [3, 4, 5, 6, 7, 8, 9, 10, 11]);
    }

    public static function canViewAny(): bool
    {
        return auth()->user()->isAdmin() || auth()->user()->isSuperAdmin() || auth()->user()->isStaffBankDPTaspen() || auth()->user()->isApprovalBankDPTaspen();
    }
}
