<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\BanpotMaster;
use Filament\Resources\Resource;
use App\Models\BanpotMasterNeedProses;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\Summarizers\Summarizer;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\BanpotMasterNeedProsesResource\Pages;
use App\Filament\Resources\BanpotMasterNeedProsesResource\RelationManagers;

class BanpotMasterNeedProsesResource extends Resource
{
    protected static ?string $model = BanpotMasterNeedProses::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Banpot';
    protected static ?int $navigationSort = 11;

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
                    ->default(10000)
                    ->prefix('Rp')
                    ->live(onBlur: false)
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
                    ->dehydrated()
                    ->disabled()
                    ->default(0)  // Changed from 0.00 to '0'
                    ->prefix('Rp')
                    ->live(onBlur: false)
                    ->afterStateUpdated(function ($state, callable $set) {


                        $numericValue = preg_replace('/[^0-9]/', '', $state);


                        $formattedValue = number_format((int)$numericValue, 0, ',', '.');
                        $set('saldo_after_pinbuk', $formattedValue);
                    })
                    ->dehydrateStateUsing(function ($state) {
                        // Clean the value for database storage
                        $cleanValue = preg_replace('/[^0-9]/', '', $state);

                        // Return 0 if empty, otherwise return the numeric value
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
                                // Check if the value ends with ".00"
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
                    ->options([
                        '1' => 'requested',
                        '2' => 'approved',
                        '3' => 'on process',
                        '4' => 'success',
                        '5' => 'failed',
                        '6' => 'duplicate'
                    ])
                    ->default(1),
                Forms\Components\Textarea::make('keterangan')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('user_id')
                    ->hidden()
                    ->required()
                    ->numeric(),
                Forms\Components\Select::make('created_by')
                    ->relationship('user', 'name')
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
                Tables\Columns\TextColumn::make('user.name')
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
                    ->sortable()
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
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('bank_transfer')
                    ->searchable(),
                Tables\Columns\TextColumn::make('rek_transfer')
                    ->searchable(),
                Tables\Columns\TextColumn::make('mitraMaster.jenis_fee')
                    ->searchable(),
                Tables\Columns\TextColumn::make('mitraMaster.fee_banpot')
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
                    ->formatStateUsing(fn($state) => 'Rp ' . number_format($state, 0, ',', '.'))
                    ->summarize([
                        Summarizer::make()
                            ->label('Total Pembayaran')
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
                Tables\Columns\TextColumn::make('keterangan')
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
            'index' => Pages\ListBanpotMasterNeedProses::route('/'),
            // 'create' => Pages\CreateBanpotMasterNeedProses::route('/create'),
            'view' => Pages\ViewBanpotMasterNeedProses::route('/{record}'),
            'edit' => Pages\EditBanpotMasterNeedProses::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ])->where('status_banpot', 2);
    }

    public static function canViewAny(): bool
    {
        return auth()->user()->isAdmin() || auth()->user()->isSuperAdmin();
    }
}