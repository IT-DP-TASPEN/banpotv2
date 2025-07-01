<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\BanpotMaster;
use Filament\Resources\Resource;
use App\Models\ParameterFeeBanpot;
use Illuminate\Auth\Events\Failed;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use App\Models\IdentitasMitraMaster;
use App\Models\BanpotMasterCompleted;
use Filament\Forms\Components\Hidden;

use function Laravel\Prompts\warning;
use GrahamCampbell\ResultType\Success;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Widgets\BanpotStatusWidget;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\Summarizers\Summarizer;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\BanpotMasterResource\Pages;
use App\Filament\Resources\BanpotMasterResource\RelationManagers;

class BanpotMasterResource extends Resource
{
    protected static ?string $model = BanpotMaster::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-arrow-up';
    protected static ?string $navigationGroup = 'Banpot';
    protected static ?string $navigationLabel = 'Request Bantuan Potong';
    protected static ?int $navigationSort = 8;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Forms\Components\TextInput::make('banpot_id')
                //     ->required()
                //     ->unique(ignoreRecord: true)
                //     ->default(function () {
                //         // Ambil banpot_id terakhir dari banpot_masters
                //         $latestMaster = BanpotMaster::orderBy('id', 'desc')->first();
                //         $latestCompleted = BanpotMasterCompleted::orderBy('id', 'desc')->first();

                //         $lastNumberMaster = $latestMaster ? (int) str_replace('B', '', $latestMaster->banpot_id) : 0;
                //         $lastNumberCompleted = $latestCompleted ? (int) str_replace('B', '', $latestCompleted->banpot_id) : 0;

                //         // Ambil nomor urut terbesar dari kedua table
                //         $nextNumber = max($lastNumberMaster, $lastNumberCompleted) + 1;

                //         return 'B' . str_pad($nextNumber, 15, '0', STR_PAD_LEFT);
                //     })

                //     ->disabled()
                //     ->dehydrated()
                //     ->columnSpanFull()
                //     ->extraInputAttributes(['style' => 'text-align: center;']),
                Forms\Components\TextInput::make('nama_nasabah')
                    ->maxLength(255),
                Forms\Components\TextInput::make('rek_tabungan')
                    ->helperText('*Mohon masukan dengan format titik, contoh : 00.000.00000')
                    ->maxLength(255),

                Forms\Components\TextInput::make('notas')
                    ->maxLength(255),
                Forms\Components\TextInput::make('rek_kredit')
                    ->helperText('*Mohon masukan dengan format titik, contoh : 00.000.00000')
                    ->maxLength(255),
                Forms\Components\TextInput::make('tenor')
                    ->suffix('Bulan'),
                Forms\Components\TextInput::make('angsuran_ke')
                    ->prefix('Angsuran-ke'),
                Forms\Components\DatePicker::make('tat_kredit')
                    ->label('Tanggal Realisasi'),
                Forms\Components\DatePicker::make('tmt_kredit')

                    ->label('Tanggal Jatuh Tempo'),
                Forms\Components\TextInput::make('gaji_pensiun')
                    ->required()
                    ->prefix('Rp')
                    ->live(onBlur: false)
                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                        $gajiPensiun = (int)preg_replace('/[^0-9]/', '', $state);
                        $nominalPotongan = (int)preg_replace('/[^0-9]/', '', $get('nominal_potongan'));

                        // Jika kedua field sudah diisi
                        if ($gajiPensiun > 0 && $nominalPotongan > 0) {
                            $user = auth()->user();
                            $param = ParameterFeeBanpot::where('mitra_id', $user->mitra_id)->first();
                            $saldoMengendap = $param ? (int)$param->saldo_mengendap : 0;

                            $set('saldo_mengendap', number_format($saldoMengendap, 0, ',', '.'));

                            $jumlahTertagih = $nominalPotongan - $saldoMengendap;
                            $set('jumlah_tertagih', number_format($jumlahTertagih, 0, ',', '.'));

                            $pinbukSisaGaji = $gajiPensiun - $jumlahTertagih;
                            $set('pinbuk_sisa_gaji', number_format($pinbukSisaGaji, 0, ',', '.'));

                            $saldoAfterPinbuk = $gajiPensiun - $pinbukSisaGaji;
                            $set('saldo_after_pinbuk', number_format($saldoAfterPinbuk, 0, ',', '.'));
                        }
                    })
                    ->dehydrateStateUsing(fn($state) => preg_replace('/[^0-9]/', '', $state))
                    ->formatStateUsing(fn($state) => is_numeric($state) ? number_format((int)$state, 0, ',', '.') : $state)
                    ->rules([
                        fn() => function (string $attribute, $value, \Closure $fail) {
                            if (preg_match('/\.00$/', $value)) {
                                $fail("Format nominal tidak valid. Jangan gunakan desimal (.00).");
                            }
                        },
                    ]),

                Forms\Components\TextInput::make('nominal_potongan')
                    ->required()
                    ->prefix('Rp')
                    ->live(onBlur: false)
                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                        $gajiPensiun = (int)preg_replace('/[^0-9]/', '', $get('gaji_pensiun'));
                        $nominalPotongan = (int)preg_replace('/[^0-9]/', '', $state);

                        if ($gajiPensiun > 0 && $nominalPotongan > 0) {
                            $user = auth()->user();
                            $param = ParameterFeeBanpot::where('mitra_id', $user->mitra_id)->first();
                            $saldoMengendap = $param ? (int)$param->saldo_mengendap : 0;

                            $set('saldo_mengendap', number_format($saldoMengendap, 0, ',', '.'));

                            $jumlahTertagih = $nominalPotongan - $saldoMengendap;
                            $set('jumlah_tertagih', number_format($jumlahTertagih, 0, ',', '.'));

                            $pinbukSisaGaji = $gajiPensiun - $jumlahTertagih;
                            $set('pinbuk_sisa_gaji', number_format($pinbukSisaGaji, 0, ',', '.'));

                            $saldoAfterPinbuk = $gajiPensiun - $pinbukSisaGaji;
                            $set('saldo_after_pinbuk', number_format($saldoAfterPinbuk, 0, ',', '.'));
                        }
                    })
                    ->dehydrateStateUsing(fn($state) => preg_replace('/[^0-9]/', '', $state))
                    ->formatStateUsing(fn($state) => is_numeric($state) ? number_format((int)$state, 0, ',', '.') : $state)
                    ->rules([
                        fn() => function (string $attribute, $value, \Closure $fail) {
                            if (preg_match('/\.00$/', $value)) {
                                $fail("Format nominal tidak valid. Jangan gunakan desimal (.00).");
                            }
                        },
                    ]),

                Forms\Components\TextInput::make('saldo_mengendap')
                    ->required()
                    ->prefix('Rp')
                    ->disabled()
                    ->dehydrated()
                    ->formatStateUsing(fn($state) => number_format((int)$state, 0, ',', '.'))
                    ->dehydrateStateUsing(fn($state) => preg_replace('/[^0-9]/', '', $state)),

                Forms\Components\TextInput::make('jumlah_tertagih')
                    ->required()
                    ->prefix('Rp')
                    ->disabled()
                    ->dehydrated()
                    ->dehydrateStateUsing(fn($state) => preg_replace('/[^0-9]/', '', $state))
                    ->formatStateUsing(fn($state) => is_numeric($state) ? number_format((int)$state, 0, ',', '.') : $state),

                Forms\Components\TextInput::make('pinbuk_sisa_gaji')
                    ->required()
                    ->prefix('Rp')
                    ->disabled()
                    ->dehydrated()
                    ->dehydrateStateUsing(fn($state) => preg_replace('/[^0-9]/', '', $state))
                    ->formatStateUsing(fn($state) => is_numeric($state) ? number_format((int)$state, 0, ',', '.') : $state),

                Forms\Components\TextInput::make('saldo_after_pinbuk')
                    ->required()
                    ->prefix('Rp')
                    ->disabled()
                    ->dehydrated()
                    ->dehydrateStateUsing(fn($state) => preg_replace('/[^0-9]/', '', $state))
                    ->formatStateUsing(fn($state) => is_numeric($state) ? number_format((int)$state, 0, ',', '.') : $state)
                    ->rules([
                        fn() => function (string $attribute, $value, \Closure $fail) {
                            if (preg_match('/\.00$/', $value)) {
                                $fail("Format nominal tidak valid. Jangan gunakan desimal (.00).");
                            }
                        },
                    ]),

                Forms\Components\TextInput::make('bank_transfer')
                    ->maxLength(255),
                Forms\Components\TextInput::make('rek_transfer')
                    ->maxLength(255),
                Forms\Components\Select::make('status_banpot')
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
                Forms\Components\Textarea::make('keterangan')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('user_id')
                    ->hidden()
                    ->required()
                    ->numeric(),
                Forms\Components\Select::make('created_by')
                    ->relationship('user', 'name')
                    ->visible(fn() => auth()->user()->isAdmin() || auth()->user()->isSuperAdmin())
                    ->disabled()
                    ->dehydrated()
                    ->default(auth()->id()),
                Forms\Components\TextInput::make('updated_by')
                    ->hidden()
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('banpot_id')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nama_nasabah')
                    ->searchable(),
                Tables\Columns\TextColumn::make('rek_tabungan')
                    ->searchable(),
                Tables\Columns\TextColumn::make('notas')
                    ->searchable(),
                Tables\Columns\TextColumn::make('rek_kredit')
                    ->searchable(),
                Tables\Columns\TextColumn::make('tenor')
                    ->searchable(),
                Tables\Columns\TextColumn::make('angsuran_ke')
                    ->searchable(),
                Tables\Columns\TextColumn::make('tat_kredit')
                    ->label('Tanggal Realisasi')
                    ->searchable(),
                Tables\Columns\TextColumn::make('tmt_kredit')
                    ->label('Tanggal Jatuh Tempo')
                    ->searchable(),
                Tables\Columns\TextColumn::make('gaji_pensiun')
                    ->numeric()
                    ->sortable()
                    ->summarize(Sum::make()),
                Tables\Columns\TextColumn::make('nominal_potongan')
                    ->numeric()
                    ->sortable()
                    ->summarize(Sum::make()),
                Tables\Columns\TextColumn::make('saldo_mengendap')
                    ->numeric()
                    ->summarize(Sum::make()),
                Tables\Columns\TextColumn::make('jumlah_tertagih')
                    ->numeric()
                    ->sortable()
                    ->summarize(Sum::make()),
                Tables\Columns\TextColumn::make('pinbuk_sisa_gaji')
                    ->numeric()
                    ->sortable()
                    ->summarize(Sum::make()),
                Tables\Columns\TextColumn::make('saldo_after_pinbuk')
                    ->hidden()
                    ->numeric()
                    ->sortable()
                    ->summarize(Sum::make()),
                Tables\Columns\TextColumn::make('bank_transfer')
                    ->hidden()
                    ->searchable(),
                Tables\Columns\TextColumn::make('rek_transfer')
                    ->hidden()
                    ->searchable(),
                Tables\Columns\TextColumn::make('pembayaran')
                    ->label('Fee Banpot')
                    ->getStateUsing(function ($record) {
                        $jenisFee = $record->mitraMaster->jenis_fee ?? 0;
                        $gajiPensiun = $record->gaji_pensiun ?? 0;
                        $nominalPotongan = $record->nominal_potongan ?? 0;
                        $feeBanpot = $record->mitraMaster->fee_banpot ?? 0;

                        if ($jenisFee == 1) {
                            $hasil = $gajiPensiun * ($feeBanpot / 100);
                        } elseif ($jenisFee == 2) {
                            $hasil = $nominalPotongan * ($feeBanpot / 100);
                        } else {
                            $hasil = 0;
                        }
                        return $hasil; // Return numeric value for summarizer
                    })
                    ->formatStateUsing(fn($state) =>  number_format($state, 0, ',', '.'))
                    ->summarize([
                        Summarizer::make()
                            ->label('Total')
                            ->using(function () {
                                return BanpotMaster::query()
                                    ->with('mitraMaster')
                                    ->get()
                                    ->sum(function ($record) {
                                        $jenisFee = $record->mitraMaster->jenis_fee ?? 0;
                                        $gajiPensiun = $record->gaji_pensiun ?? 0;
                                        $nominalPotongan = $record->nominal_potongan ?? 0;
                                        $feeBanpot = $record->mitraMaster->fee_banpot ?? 0;

                                        if ($jenisFee == 1) {
                                            return $gajiPensiun * ($feeBanpot / 100);
                                        } elseif ($jenisFee == 2) {
                                            return $nominalPotongan * ($feeBanpot / 100);
                                        }
                                        return 0;
                                    });
                            })
                            ->formatStateUsing(fn($state) => 'Rp ' . number_format($state, 0, ',', '.'))
                            ->numeric()
                    ]),
                Tables\Columns\IconColumn::make('rek_tabungan_validasi')
                    ->label('Validasi Rek Tabungan')
                    ->boolean()
                    ->color(fn($state) => match ($state) {
                        true => 'success',
                        false => 'danger',
                        default => 'warning',
                    })
                    ->getStateUsing(function ($record): bool {
                        $identity = $record->identitasByNotas;
                        $user = $record->user;

                        if (!$identity || !$user) {
                            return false;
                        }

                        $mitraMatch = $user->mitra_id == $identity->mitra_id;

                        $rekMatch = !empty($record->rek_tabungan)
                            && !empty($identity->rek_tabungan)
                            && $record->rek_tabungan == $identity->rek_tabungan;

                        return $mitraMatch && $rekMatch;
                    }),
                Tables\Columns\IconColumn::make('notas_validasi')
                    ->label('Validasi Notas')
                    ->boolean()
                    ->color(fn($state) => match ($state) {
                        true => 'success',
                        false => 'danger',
                        default => 'warning',
                    })
                    ->getStateUsing(function ($record): bool {
                        $identity = $record->identitasByNotas;
                        $user = $record->user;

                        if (!$identity || !$user) {
                            return false;
                        }

                        $mitraMatch = $user->mitra_id == $identity->mitra_id;

                        $notasMatch = !empty($record->notas)
                            && !empty($identity->notas)
                            && $record->notas == $identity->notas;

                        return $mitraMatch && $notasMatch;
                    }),
                Tables\Columns\IconColumn::make('dapem_validasi')
                    ->label('Validasi Dapem')
                    ->boolean()
                    ->color(function ($state) {
                        return match ($state) {
                            true => 'success',
                            false => 'danger',
                        };
                    })
                    ->getStateUsing(function ($record): bool {
                        $user = $record->user;
                        $dapem = $record->dapemMaster;

                        if (!$user || !$dapem || empty($dapem->notas)) {
                            return false;
                        }

                        // Cek apakah notas dapem ini sudah dimiliki oleh mitra lain di identitas master
                        $sudahDipakaiMitraLain = IdentitasMitraMaster::where('notas', $dapem->notas)
                            ->where('mitra_id', '!=', $user->mitra_id)
                            ->exists();

                        // Kalau sudah dipakai mitra lain, return false
                        if ($sudahDipakaiMitraLain) {
                            return false;
                        }

                        // Kalau belum dipakai mitra lain dan dapem notas sama, return true
                        return $record->notas == $dapem->notas;
                    }),
                Tables\Columns\IconColumn::make('kode_otentifikasi')
                    ->label('Validasi Otentifikasi')
                    ->boolean()
                    ->color(fn($state) => match ($state) {
                        true => 'success',
                        false => 'danger',
                    })
                    ->getStateUsing(function ($record): bool {
                        $user = $record->user;
                        $oten = $record->otenMaster?->sortByDesc('log_date_time')?->first();

                        if (!$user || !$oten) {
                            return false;
                        }

                        $sudahDipakaiMitraLain = IdentitasMitraMaster::where('notas', $record->notas)
                            ->where('mitra_id', '!=', $user->mitra_id)
                            ->exists();

                        if ($sudahDipakaiMitraLain) {
                            return false;
                        }

                        return in_array($oten->kode_otentifikasi, [11, 13, 14, 15]);
                    }),
                Tables\Columns\IconColumn::make('enrollment_status')
                    ->label('Validasi Enrollment')
                    ->boolean()
                    ->color(fn($state) => match ($state) {
                        true => 'success',
                        false => 'danger',
                    })
                    ->getStateUsing(function ($record): bool {
                        $user = $record->user;
                        $oten = $record->otenMaster?->sortByDesc('log_date_time')?->first();

                        if (!$user || !$oten) {
                            return false;
                        }

                        $sudahDipakaiMitraLain = IdentitasMitraMaster::where('notas', $record->notas)
                            ->where('mitra_id', '!=', $user->mitra_id)
                            ->exists();

                        if ($sudahDipakaiMitraLain) {
                            return false;
                        }

                        return in_array($oten->kode_otentifikasi, [11, 13, 14, 30]);
                    }),


                Tables\Columns\TextColumn::make('validasi')
                    ->badge()
                    ->separator(',')
                    ->listWithLineBreaks()
                    ->color(function ($state) {
                        return $state === 'Done' ? 'success' : 'danger';
                    })
                    ->getStateUsing(function ($record) {
                        $messages = [];
                        $user = $record->user;

                        // ===== VALIDASI REKENING =====
                        $rekExist = IdentitasMitraMaster::where('rek_tabungan', $record->rek_tabungan)->first();

                        if ($rekExist) {
                            if ($rekExist->mitra_id != $user->mitra_id) {
                                $messages[] = 'Rekening sudah didaftarkan oleh mitra lain';
                            } elseif ($rekExist->rek_tabungan != $record->rek_tabungan) {
                                $messages[] = 'Rekening belum cocok dengan identitas mitra';
                            }
                        } else {
                            $messages[] = 'Rekening belum terdaftar di identitas mitra';
                        }

                        // ===== VALIDASI NOTAS =====
                        $notasExist = IdentitasMitraMaster::where('notas', $record->notas)->first();

                        if ($notasExist) {
                            if ($notasExist->mitra_id != $user->mitra_id) {
                                $messages[] = 'Notas sudah didaftarkan oleh mitra lain';
                            } elseif ($notasExist->notas != $record->notas) {
                                $messages[] = 'Notas belum cocok dengan identitas mitra';
                            }
                        } else {
                            $messages[] = 'Notas belum terdaftar di identitas mitra';
                        }

                        // ===== VALIDASI DAPEM =====
                        $dapemExist = IdentitasMitraMaster::where('notas', $record->notas)
                            ->where('mitra_id', '!=', $user->mitra_id)
                            ->exists();

                        if (!$record->dapemMaster || empty($record->dapemMaster->notas)) {
                            $messages[] = 'Dapem belum ditemukan';
                        } elseif ($dapemExist) {
                            $messages[] = 'Dapem sudah didaftarkan oleh mitra lain';
                        }

                        // ===== VALIDASI OTEN =====
                        // VALIDASI OTENTIFIKASI
                        $oten = $record->otenMaster?->sortByDesc('log_date_time')?->first();
                        $otenExist = IdentitasMitraMaster::where('notas', $record->notas)
                            ->where('mitra_id', '!=', $user->mitra_id)
                            ->exists();

                        $otenValid = $oten && in_array($oten->kode_otentifikasi, [11, 13, 14, 15]);

                        if (!$otenValid) {
                            $messages[] = 'Belum Otentifikasi';
                        } elseif ($otenExist) {
                            $messages[] = 'Oten sudah didaftarkan oleh mitra lain';
                        }

                        // VALIDASI ENROLLMENT
                        $enrollValid = $oten && in_array($oten->kode_otentifikasi, [13, 14, 15, 30]);

                        if (!$enrollValid) {
                            $messages[] = 'Belum Enrollment';
                        } elseif ($otenExist) {
                            $messages[] = 'Enrollment sudah didaftarkan oleh mitra lain';
                        }

                        return empty($messages) ? 'Done' : implode(', ', $messages);
                    }),

                Tables\Columns\TextColumn::make('keterangan'),
                Tables\Columns\TextColumn::make('status_banpot')
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
            // Tables\Filters\SelectFilter::make('status_banpot')
            //     ->label('Status Banpot')
            //     ->options([
            //         '1' => 'Request',
            //         '2' => 'Checked by Mitra',
            //         '3' => 'Approved by Mitra',
            //         '4' => 'Rejected by Mitra',
            //         '5' => 'Canceled by Mitra',
            //         '6' => 'Checked by Bank DP Taspen',
            //         '7' => 'Approved by Bank DP Taspen',
            //         '8' => 'Rejected by Bank DP Taspen',
            //         '9' => 'On Process',
            //         '10' => 'Success',
            //         '11' => 'Failed',
            //     ])
            //     ->multiple(),

            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('updatestatusbanpot')
                        ->label('Update Status Banpot')
                        ->icon('heroicon-m-pencil-square')
                        ->form([
                            Forms\Components\Select::make('status_banpot')
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
                                    'status_banpot' => $data['status_banpot'],
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
                ])
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBanpotMasters::route('/'),
            'create' => Pages\CreateBanpotMaster::route('/create'),
            'view' => Pages\ViewBanpotMaster::route('/{record}'),
            'edit' => Pages\EditBanpotMaster::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $user = auth()->user();
        return parent::getEloquentQuery()
            ->when(
                $user->roles == '6',
                fn($query) => $query
                    ->where('created_by', $user->id)
                    ->whereHas('creator', function ($q) use ($user) {
                        $q->where('mitra_id', $user->mitra_id)
                            ->where('mitra_cabang_id', $user->mitra_cabang_id);
                    })
            )
            ->when(
                $user->roles == '4',
                fn($query) => $query
                    ->whereHas('creator', function ($q) use ($user) {
                        $q->where('roles', '6')
                            ->where('mitra_id', $user->mitra_id)
                            ->where('mitra_cabang_id', $user->mitra_cabang_id);
                    })
            )
            ->when(
                !in_array($user->roles, ['4', '6']),
                fn($query) => $query // Roles lain tanpa filter
            );
    }

    public static function canViewAny(): bool
    {
        return auth()->user()->isAdmin() || auth()->user()->isSuperAdmin() || auth()->user()->isApprovalBankDPTaspen() || auth()->user()->isStaffBankDPTaspen() || auth()->user()->isStaffMitraPusat();
    }
}
