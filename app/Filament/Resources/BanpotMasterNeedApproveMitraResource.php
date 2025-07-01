<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\BanpotMaster;
use Filament\Resources\Resource;
use Illuminate\Support\Collection;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use App\Models\BanpotMasterNeedApproveMitra;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\Summarizers\Summarizer;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\BanpotMasterNeedApproveMitraResource\Pages;
use App\Filament\Resources\BanpotMasterNeedApproveMitraResource\RelationManagers;

class BanpotMasterNeedApproveMitraResource extends Resource
{
    protected static ?string $model = BanpotMasterNeedApproveMitra::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-check';
    protected static ?string $navigationGroup = 'Banpot';

    protected static ?string $navigationLabel = 'Approval Bantuan Potong Mitra';
    protected static ?int $navigationSort = 9;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('banpot_id')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->default(function () {
                        // Ambil ID transaksi terakhir
                        $latest = BanpotMaster::orderBy('id', 'desc')->first();

                        // Generate nomor urut
                        $sequence = $latest ?
                            (int) str_replace('B', '', $latest->banpot_id) + 1 :
                            1;

                        return 'B' . str_pad($sequence, 5, '0', STR_PAD_LEFT);
                    })
                    ->disabled()
                    ->dehydrated()
                    ->columnSpanFull()
                    ->extraInputAttributes(['style' => 'text-align: center;']),
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
                Forms\Components\TextInput::make('gaji_pensiun')
                    ->required()
                    ->prefix('Rp')
                    ->live(onBlur: false)
                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                        // Remove any non-numeric characters
                        $numericValue = preg_replace('/[^0-9]/', '', $state);

                        // Only format if there's a value
                        if ($numericValue !== '') {
                            // Format with thousand separators
                            $formattedValue = number_format((int)$numericValue, 0, ',', '.');
                            $set('gaji_pensiun', $formattedValue);
                        }

                        // Calculate pinbuk_sisa_gaji
                        $gajiPensiun = (int)preg_replace('/[^0-9]/', '', $state);
                        $jumlahTertagih = (int)preg_replace('/[^0-9]/', '', $get('jumlah_tertagih') ?? '0');

                        if ($gajiPensiun > 0 && $jumlahTertagih > 0) {
                            $pinbukSisaGaji = $gajiPensiun - $jumlahTertagih;
                            $set('pinbuk_sisa_gaji', number_format($pinbukSisaGaji, 0, ',', '.'));

                            $saldoAfterPinbuk = $gajiPensiun - $pinbukSisaGaji;
                            $set('saldo_after_pinbuk', number_format($saldoAfterPinbuk, 0, ',', '.'));
                        }
                    })
                    ->dehydrateStateUsing(fn($state) => preg_replace('/[^0-9]/', '', $state))
                    ->formatStateUsing(function ($state) {
                        // When loading existing value from database
                        if (is_numeric($state)) {
                            return number_format((int)$state, 0, ',', '.');
                        }
                        return $state;
                    })
                    ->rules([
                        function () {
                            return function (string $attribute, $value, \Closure $fail) {
                                // Check if the value ends with ".00"
                                if (preg_match('/\.00$/', $value)) {
                                    $fail("Format nominal tidak valid. Jangan gunakan desimal (.00).");
                                }
                            };
                        },
                    ]),

                Forms\Components\TextInput::make('nominal_potongan')
                    ->required()
                    ->prefix('Rp')
                    ->live(onBlur: false)
                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                        // Remove any non-numeric characters
                        $numericValue = preg_replace('/[^0-9]/', '', $state);

                        // Only format if there's a value
                        if ($numericValue !== '') {
                            // Format with thousand separators
                            $formattedValue = number_format((int)$numericValue, 0, ',', '.');
                            $set('nominal_potongan', $formattedValue);
                            $set('saldo_mengendap', number_format(10000, 0, ',', '.')); // Set default saldo_mengendap
                        }

                        // Calculate jumlah_tertagih
                        $nominalPotongan = (int)preg_replace('/[^0-9]/', '', $state);
                        $saldoMengendap = (int)preg_replace('/[^0-9]/', '', $get('saldo_mengendap') ?? '0');

                        if ($nominalPotongan > 0 && $saldoMengendap > 0) {
                            $jumlahTertagih = $nominalPotongan - $saldoMengendap;
                            $set('jumlah_tertagih', number_format($jumlahTertagih, 0, ',', '.'));

                            // Calculate pinbuk_sisa_gaji after jumlah_tertagih is updated
                            $gajiPensiun = (int)preg_replace('/[^0-9]/', '', $get('gaji_pensiun') ?? '0');
                            if ($gajiPensiun > 0) {
                                $pinbukSisaGaji = $gajiPensiun - $jumlahTertagih;
                                $set('pinbuk_sisa_gaji', number_format($pinbukSisaGaji, 0, ',', '.'));

                                $saldoAfterPinbuk = $gajiPensiun - $pinbukSisaGaji;
                                $set('saldo_after_pinbuk', number_format($saldoAfterPinbuk, 0, ',', '.'));
                            }
                        }
                    })
                    ->dehydrateStateUsing(fn($state) => preg_replace('/[^0-9]/', '', $state))
                    ->formatStateUsing(function ($state) {
                        // When loading existing value from database
                        if (is_numeric($state)) {
                            return number_format((int)$state, 0, ',', '.');
                        }
                        return $state;
                    })
                    ->rules([
                        function () {
                            return function (string $attribute, $value, \Closure $fail) {
                                // Check if the value ends with ".00"
                                if (preg_match('/\.00$/', $value)) {
                                    $fail("Format nominal tidak valid. Jangan gunakan desimal (.00).");
                                }
                            };
                        },
                    ]),

                Forms\Components\TextInput::make('saldo_mengendap')
                    ->required()
                    ->prefix('Rp')
                    ->live(onBlur: false)
                    ->disabled() // Make it read-only since it's calculated automatically
                    ->dehydrated()
                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                        // Remove any non-numeric characters
                        $numericValue = preg_replace('/[^0-9]/', '', $state);

                        // Only format if there's a value
                        if ($numericValue !== '') {
                            // Format with thousand separators
                            $formattedValue = number_format((int)$numericValue, 0, ',', '.');
                            $set('saldo_mengendap', $formattedValue);
                        }

                        // Calculate jumlah_tertagih
                        $saldoMengendap = (int)preg_replace('/[^0-9]/', '', $state);
                        $nominalPotongan = (int)preg_replace('/[^0-9]/', '', $get('nominal_potongan') ?? '0');

                        if ($nominalPotongan > 0 && $saldoMengendap > 0) {
                            $jumlahTertagih = $nominalPotongan - $saldoMengendap;
                            $set('jumlah_tertagih', number_format($jumlahTertagih, 0, ',', '.'));

                            // Calculate pinbuk_sisa_gaji after jumlah_tertagih is updated
                            $gajiPensiun = (int)preg_replace('/[^0-9]/', '', $get('gaji_pensiun') ?? '0');
                            if ($gajiPensiun > 0) {
                                $pinbukSisaGaji = $gajiPensiun - $jumlahTertagih;
                                $set('pinbuk_sisa_gaji', number_format($pinbukSisaGaji, 0, ',', '.'));
                            }
                        }
                    })
                    ->dehydrateStateUsing(fn($state) => preg_replace('/[^0-9]/', '', $state))
                    ->formatStateUsing(function ($state) {
                        // When loading existing value from database
                        if (is_numeric($state)) {
                            return number_format((int)$state, 0, ',', '.');
                        }
                        return $state;
                    })
                    ->rules([
                        function () {
                            return function (string $attribute, $value, \Closure $fail) {
                                // Check if the value ends with ".00"
                                if (preg_match('/\.00$/', $value)) {
                                    $fail("Format nominal tidak valid. Jangan gunakan desimal (.00).");
                                }
                            };
                        },
                    ]),

                Forms\Components\TextInput::make('jumlah_tertagih')
                    ->required()
                    ->prefix('Rp')
                    ->disabled() // Make it read-only since it's calculated automatically
                    ->dehydrated() // Ensure the value is saved to database
                    ->dehydrateStateUsing(fn($state) => preg_replace('/[^0-9]/', '', $state))
                    ->formatStateUsing(function ($state) {
                        // When loading existing value from database
                        if (is_numeric($state)) {
                            return number_format((int)$state, 0, ',', '.');
                        }
                        return $state;
                    })
                    ->helperText('Field ini akan terisi otomatis berdasarkan penjumlahan Nominal Potongan dan Saldo Mengendap'),
                Forms\Components\TextInput::make('pinbuk_sisa_gaji')
                    ->required()
                    ->prefix('Rp')
                    ->disabled() // Make it read-only since it's calculated automatically
                    ->dehydrated() // Ensure the value is saved to database
                    ->dehydrateStateUsing(fn($state) => preg_replace('/[^0-9]/', '', $state))
                    ->formatStateUsing(function ($state) {
                        // When loading existing value from database
                        if (is_numeric($state)) {
                            return number_format((int)$state, 0, ',', '.');
                        }
                        return $state;
                    })
                    ->helperText('Field ini akan terisi otomatis berdasarkan pengurangan Nominal Gaji dan Jumlah Tertagih'),
                Forms\Components\TextInput::make('saldo_after_pinbuk')
                    ->required()
                    ->prefix('Rp')
                    ->disabled() // Make it read-only
                    ->dehydrated() // Ensure the value is saved to database
                    ->afterStateUpdated(function ($state, callable $set) {
                        // Format the value for display
                        $numericValue = preg_replace('/[^0-9]/', '', $state);
                        if ($numericValue !== '') {
                            $formattedValue = number_format((int)$numericValue, 0, ',', '.');
                            $set('saldo_after_pinbuk', $formattedValue);
                        }
                    })
                    ->dehydrateStateUsing(function ($state) {
                        // Clean the value for database storage
                        $cleanValue = preg_replace('/[^0-9]/', '', $state);
                        return $cleanValue === '' ? 0 : (int)$cleanValue;
                    })
                    ->formatStateUsing(function ($state) {
                        // When loading existing value from database
                        if ($state === null || $state === '') {
                            return '0';
                        }
                        if (is_numeric($state)) {
                            return number_format((int)$state, 0, ',', '.');
                        }
                        return $state;
                    })
                    ->rules([
                        function () {
                            return function (string $attribute, $value, \Closure $fail) {
                                if (preg_match('/\.00$/', $value)) {
                                    $fail("Format nominal tidak valid. Jangan gunakan desimal (.00).");
                                }
                            };
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
                Tables\Columns\TextColumn::make('gaji_pensiun')
                    ->numeric()
                    ->sortable()
                    ->summarize(Sum::make()),
                Tables\Columns\TextColumn::make('nominal_potongan')
                    ->numeric()
                    ->sortable()
                    ->summarize(Sum::make()),
                Tables\Columns\TextColumn::make('saldo_mengendap')
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
                Tables\Columns\TextColumn::make('mitraMaster.jenis_fee')
                    ->visible(false)
                    ->searchable(),
                Tables\Columns\TextColumn::make('mitraMaster.fee_banpot')
                    ->visible(false)
                    ->searchable(),
                Tables\Columns\TextColumn::make('pembayaran')
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
                Tables\Columns\TextColumn::make('identityMaster.rek_tabungan')
                    ->label('Rek Tabungan')
                    ->badge()
                    ->searchable()
                    ->sortable()
                    ->hidden(),
                Tables\Columns\IconColumn::make('rek_tabungan_validasi')
                    ->label('Validasi Rek Tabungan')
                    ->boolean()
                    ->color(function ($state) {
                        return match ($state) {
                            true => 'success',
                            false => 'danger',
                            default => 'warning',
                        };
                    })
                    ->getStateUsing(
                        fn($record): bool =>
                        // Return true if valid, false otherwise
                        isset($record->identityMaster->rek_tabungan)
                            && !empty($record->rek_tabungan)
                            && !empty($record->identityMaster->rek_tabungan)
                            && $record->rek_tabungan == $record->identityMaster->rek_tabungan
                    ),
                // Tables\Columns\TextColumn::make('identityMaster.notas')
                //     ->label('Validasi Notas')
                //     ->hidden()
                //     ->badge()
                //     ->sortable(),
                // Tables\Columns\TextColumn::make('notas_validasi')
                //     ->label('Validasi Notas')
                //     ->badge()
                //     ->color(function ($state) {
                //         return match ($state) {
                //             'Valid' => 'success',
                //             'Tidak Valid' => 'danger',
                //             default => 'warning',
                //         };
                //     })
                //     ->getStateUsing(function ($record) {
                //         $rekTabungan = $record->notas;
                //         $identityRekTabungan = $record->identityMaster->notas ?? null;

                //         // Jika tidak ada data identityMaster
                //         if (!$identityRekTabungan) {
                //             return 'Belum Terdaftar/Terdaftar oleh mitra lain';
                //         }

                //         // Jika rek tabungan kosong
                //         if (empty($rekTabungan) || empty($identityRekTabungan)) {
                //             return 'Data Kosong';
                //         }

                //         // Bandingkan nilai rek tabungan
                //         return $rekTabungan == $identityRekTabungan ? 'Valid' : 'Tidak Valid';
                //     }),
                Tables\Columns\IconColumn::make('notas_validasi')
                    ->label('Validasi Notas')
                    ->boolean()
                    ->color(function ($state) {
                        return match ($state) {
                            true => 'success',
                            false => 'danger',
                            default => 'warning',
                        };
                    })
                    ->getStateUsing(
                        fn($record): bool =>
                        // Return true if valid, false otherwise
                        isset($record->identityMaster->notas)
                            && !empty($record->notas)
                            && !empty($record->identityMaster->notas)
                            && $record->notas == $record->identityMaster->notas
                    ),
                Tables\Columns\IconColumn::make('dapemMaster.notas')
                    ->label('Dapem Tersedia')
                    ->boolean()
                    ->color(function ($state) {
                        return match ($state) {
                            true => 'success',
                            false => 'danger',
                        };
                    })
                    ->getStateUsing(
                        fn($record): bool =>
                        // Return true if valid, false otherwise
                        isset($record->dapemMaster->notas)
                            && !empty($record->dapemMaster->notas)
                            && $record->notas == $record->dapemMaster->notas
                    ),
                Tables\Columns\IconColumn::make('kode_otentifikasi')
                    ->label('Status Oten')
                    ->boolean()
                    ->color(function ($state) {
                        return match ($state) {
                            true => 'success',
                            false => 'danger',
                        };
                    })
                    ->getStateUsing(
                        fn($record): bool =>
                        isset($record->otenMaster->kode_otentifikasi)
                            && in_array($record->otenMaster->kode_otentifikasi, [11, 13, 14, 15])
                    ),
                Tables\Columns\IconColumn::make('enrollment_status')
                    ->label('Status Enrollment')
                    ->boolean()
                    ->color(function ($state) {
                        return match ($state) {
                            true => 'success',
                            false => 'danger',
                        };
                    })
                    ->getStateUsing(
                        fn($record): bool =>
                        isset($record->otenMaster->kode_otentifikasi)
                            && in_array($record->otenMaster->kode_otentifikasi, [13, 14, 15, 30])
                    ),

                Tables\Columns\TextColumn::make('validasi')
                    ->badge()
                    ->separator(',')
                    ->listWithLineBreaks()
                    ->color(function ($state) {
                        return $state === 'Done' ? 'success' : 'danger';
                    })
                    ->getStateUsing(function ($record) {
                        $messages = [];

                        // Validasi Rek Tabungan
                        $rekValid = isset($record->identityMaster->rek_tabungan)
                            && !empty($record->rek_tabungan)
                            && !empty($record->identityMaster->rek_tabungan)
                            && $record->rek_tabungan == $record->identityMaster->rek_tabungan;

                        if (!$rekValid) {
                            $messages[] = 'Rekening salah/belum terdaftar/sudah di daftarkan mitra lain';
                        }

                        // Validasi Notas
                        $notasValid = isset($record->identityMaster->notas)
                            && !empty($record->notas)
                            && !empty($record->identityMaster->notas)
                            && $record->notas == $record->identityMaster->notas;

                        if (!$notasValid) {
                            $messages[] = 'Notas salah/belum terdaftar/sudah di daftarkan mitra lain';
                        }

                        // Validasi Dapem
                        $dapemValid = isset($record->dapemMaster->notas)
                            && !empty($record->dapemMaster->notas)
                            && $record->notas == $record->dapemMaster->notas;

                        if (!$dapemValid) {
                            $messages[] = 'Dapem belum tersedia';
                        }

                        // Validasi Oten
                        $otenValid = isset($record->otenMaster->kode_otentifikasi)
                            && in_array($record->otenMaster->kode_otentifikasi, [11, 13, 14, 15]);

                        if (!$otenValid) {
                            $messages[] = 'Belum Otentifikasi';
                        }

                        // Validasi Enrollment
                        $enrollValid = isset($record->otenMaster->kode_otentifikasi)
                            && in_array($record->otenMaster->kode_otentifikasi, [13, 14, 15, 30]);

                        if (!$enrollValid) {
                            $messages[] = 'Belum Enrollment';
                        }

                        // Jika semua validasi true
                        if (empty($messages)) {
                            return 'Done';
                        }

                        return implode(', ', $messages);
                    }),

                Tables\Columns\TextColumn::make('keterangan'),
                Tables\Columns\TextColumn::make('status_banpot')
                    ->label('Status')
                    ->badge()
                    ->getStateUsing(fn($record) => match ($record->status_banpot) {
                        '1' => 'requested',
                        '2' => 'approved',
                        '3' => 'on process', // Perhatikan spasi di sini
                        '4' => 'success',
                        '5' => 'failed',
                        '6' => 'duplicate', // Tambahkan default untuk nilai tidak terduga
                    })
                    ->colors([
                        'warning' => fn($state) => in_array($state, ['requested', 'on process']),
                        'success' => fn($state) => in_array($state, ['approved', 'success']),
                        'danger' => fn($state) => in_array($state, ['failed', 'duplicate']),
                    ])
                    ->sortable(),
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
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBanpotMasterNeedApproveMitras::route('/'),
            // 'create' => Pages\CreateBanpotMasterNeedApproveMitra::route('/create'),
            'view' => Pages\ViewBanpotMasterNeedApproveMitra::route('/{record}'),
            'edit' => Pages\EditBanpotMasterNeedApproveMitra::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ])
            ->where('status_banpot', 1);

        $user = auth()->user();

        // Admin can see all data with status_banpot = 1
        if ($user->isAdmin() || $user->isSuperAdmin()) {
            return $query;
        }

        // For approval mitra pusat (role 4), show all data from their mitra's branches
        if ($user->roles == '4') {
            return $query->whereHas('user', function ($q) use ($user) {
                $q->where('mitra_id', $user->mitra_id);
            });
        }

        // For other roles, show only their branch data or their own created data
        return $query->where(function ($q) use ($user) {
            $q->where('created_by', $user->id)
                ->orWhereHas('user', function ($subQuery) use ($user) {
                    $subQuery->where('mitra_cabang_id', $user->mitra_cabang_id);
                });
        });
    }

    public static function canViewAny(): bool
    {
        return auth()->user()->isAdmin() || auth()->user()->isSuperAdmin() || auth()->user()->isApprovalMitraPusat();
    }
}
