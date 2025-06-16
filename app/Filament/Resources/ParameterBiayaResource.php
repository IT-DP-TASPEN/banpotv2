<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ParameterBiayaResource\Pages;
use App\Filament\Resources\ParameterBiayaResource\RelationManagers;
use App\Models\ParameterBiaya;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ParameterBiayaResource extends Resource
{
    protected static ?string $model = ParameterBiaya::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Parameter';
    protected static ?int $navigationSort = 2;


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('biaya_id')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->default(function () {
                        // Ambil ID transaksi terakhir
                        $latest = ParameterBiaya::orderBy('id', 'desc')->first();

                        // Generate nomor urut
                        $sequence = $latest ?
                            (int) str_replace('BM', '', $latest->biaya_id) + 1 :
                            1;

                        return 'BM' . str_pad($sequence, 5, '0', STR_PAD_LEFT);
                    })
                    ->disabled()
                    ->dehydrated()
                    ->columnSpanFull()
                    ->extraInputAttributes(['style' => 'text-align: center;']),
                Forms\Components\Select::make('mitra_id')
                    ->relationship('mitraMaster', 'id', function ($query) {
                        return $query->whereNotIn('mitra_id', ['0', '00', '000']);
                    })
                    ->required()
                    ->getOptionLabelFromRecordUsing(fn($record) => "{$record->mitra_id} - {$record->nama_mitra}"),
                Forms\Components\TextInput::make('biaya_checking')
                    ->required()
                    ->prefix('Rp')
                    ->live(onBlur: false)
                    ->afterStateUpdated(function ($state, callable $set) {
                        // Remove any non-numeric characters
                        $numericValue = preg_replace('/[^0-9]/', '', $state);

                        // Only format if there's a value
                        if ($numericValue !== '') {
                            // Format with thousand separators
                            $formattedValue = number_format((int)$numericValue, 0, ',', '.');
                            $set('biaya_checking', $formattedValue);
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
                Forms\Components\TextInput::make('biaya_check_estimasi')
                    ->required()
                    ->prefix('Rp')
                    ->live(onBlur: false)
                    ->afterStateUpdated(function ($state, callable $set) {
                        // Remove any non-numeric characters
                        $numericValue = preg_replace('/[^0-9]/', '', $state);

                        // Only format if there's a value
                        if ($numericValue !== '') {
                            // Format with thousand separators
                            $formattedValue = number_format((int)$numericValue, 0, ',', '.');
                            $set('biaya_check_estimasi', $formattedValue);
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
                Forms\Components\TextInput::make('biaya_flagging_pensiun')
                    ->required()
                    ->prefix('Rp')
                    ->live(onBlur: false)
                    ->afterStateUpdated(function ($state, callable $set) {
                        // Remove any non-numeric characters
                        $numericValue = preg_replace('/[^0-9]/', '', $state);

                        // Only format if there's a value
                        if ($numericValue !== '') {
                            // Format with thousand separators
                            $formattedValue = number_format((int)$numericValue, 0, ',', '.');
                            $set('biaya_flagging_pensiun', $formattedValue);
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
                Forms\Components\TextInput::make('biaya_flagging_prapen')
                    ->required()
                    ->prefix('Rp')
                    ->live(onBlur: false)
                    ->afterStateUpdated(function ($state, callable $set) {
                        // Remove any non-numeric characters
                        $numericValue = preg_replace('/[^0-9]/', '', $state);

                        // Only format if there's a value
                        if ($numericValue !== '') {
                            // Format with thousand separators
                            $formattedValue = number_format((int)$numericValue, 0, ',', '.');
                            $set('biaya_flagging_prapen', $formattedValue);
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
                Forms\Components\TextInput::make('biaya_flagging_tht')
                    ->required()
                    ->prefix('Rp')
                    ->live(onBlur: false)
                    ->afterStateUpdated(function ($state, callable $set) {
                        // Remove any non-numeric characters
                        $numericValue = preg_replace('/[^0-9]/', '', $state);

                        // Only format if there's a value
                        if ($numericValue !== '') {
                            // Format with thousand separators
                            $formattedValue = number_format((int)$numericValue, 0, ',', '.');
                            $set('biaya_flagging_tht', $formattedValue);
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
                Forms\Components\TextInput::make('biaya_flagging_prapen_tht')
                    ->required()
                    ->prefix('Rp')
                    ->live(onBlur: false)
                    ->afterStateUpdated(function ($state, callable $set) {
                        // Remove any non-numeric characters
                        $numericValue = preg_replace('/[^0-9]/', '', $state);

                        // Only format if there's a value
                        if ($numericValue !== '') {
                            // Format with thousand separators
                            $formattedValue = number_format((int)$numericValue, 0, ',', '.');
                            $set('biaya_flagging_prapen_tht', $formattedValue);
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
                Forms\Components\TextInput::make('biaya_flagging_mutasi_tif')
                    ->required()
                    ->prefix('Rp')
                    ->live(onBlur: false)
                    ->afterStateUpdated(function ($state, callable $set) {
                        // Remove any non-numeric characters
                        $numericValue = preg_replace('/[^0-9]/', '', $state);

                        // Only format if there's a value
                        if ($numericValue !== '') {
                            // Format with thousand separators
                            $formattedValue = number_format((int)$numericValue, 0, ',', '.');
                            $set('biaya_flagging_mutasi_tif', $formattedValue);
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
                Forms\Components\TextInput::make('biaya_flagging_mutasi_tos')
                    ->required()
                    ->prefix('Rp')
                    ->live(onBlur: false)
                    ->afterStateUpdated(function ($state, callable $set) {
                        // Remove any non-numeric characters
                        $numericValue = preg_replace('/[^0-9]/', '', $state);

                        // Only format if there's a value
                        if ($numericValue !== '') {
                            // Format with thousand separators
                            $formattedValue = number_format((int)$numericValue, 0, ',', '.');
                            $set('biaya_flagging_mutasi_tos', $formattedValue);
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
                Forms\Components\TextInput::make('ppn')
                    ->inputMode('decimal')
                    ->suffix('%')
                    ->placeholder('Contoh: 3,00')
                    ->required()
                    ->numeric()
                    ->live(onBlur: true)
                    ->afterStateUpdated(function ($state, callable $set) {
                        if ($state === null || $state === '') {
                            return;
                        }

                        // Format to always display with 2 decimal places
                        $numericValue = (float) $state;
                        $formattedValue = number_format($numericValue, 2, '.', '');
                        $set('ppn', $formattedValue);
                    })
                    ->rules(['numeric', 'decimal:0,2']),
                Forms\Components\TextInput::make('pph')
                    ->inputMode('decimal')
                    ->suffix('%')
                    ->placeholder('Contoh: 3,00')
                    ->required()
                    ->numeric()
                    ->live(onBlur: true)
                    ->afterStateUpdated(function ($state, callable $set) {
                        if ($state === null || $state === '') {
                            return;
                        }

                        // Format to always display with 2 decimal places
                        $numericValue = (float) $state;
                        $formattedValue = number_format($numericValue, 2, '.', '');
                        $set('pph', $formattedValue);
                    })
                    ->rules(['numeric', 'decimal:0,2']),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('biaya_id')
                    ->searchable(),
                Tables\Columns\TextColumn::make('mitraMaster.mitra_id')
                    ->label('ID Mitra')
                    ->sortable(),
                Tables\Columns\TextColumn::make('mitraMaster.nama_mitra')
                    ->label('Nama Mitra')
                    ->sortable(),
                Tables\Columns\TextColumn::make('biaya_checking')
                    ->label('Biaya Checking')
                    ->numeric()
                    ->money('idr')
                    ->sortable(),
                Tables\Columns\TextColumn::make('biaya_check_estimasi')
                    ->label('Biaya Check Estimasi')
                    ->numeric()
                    ->money('idr')
                    ->sortable(),
                Tables\Columns\TextColumn::make('biaya_flagging_pensiun')
                    ->label('Biaya Flagging Pensiun TIF')
                    ->numeric()
                    ->money('idr')
                    ->sortable(),
                Tables\Columns\TextColumn::make('biaya_flagging_prapen')
                    ->label('Biaya Flagging Prapensiun')
                    ->numeric()
                    ->money('idr')
                    ->sortable(),
                Tables\Columns\TextColumn::make('biaya_flagging_tht')
                    ->label('Biaya Flagging THT')
                    ->numeric()
                    ->money('idr')
                    ->sortable(),
                Tables\Columns\TextColumn::make('biaya_flagging_prapen_tht')
                    ->label('Biaya Flagging Prapensiun dan THT')
                    ->numeric()
                    ->money('idr')
                    ->sortable(),
                Tables\Columns\TextColumn::make('biaya_flagging_mutasi_tif')
                    ->label('Biaya Flagging dan Mutasi TIF')
                    ->numeric()
                    ->money('idr')
                    ->sortable(),
                Tables\Columns\TextColumn::make('biaya_flagging_mutasi_tos')
                    ->label('Biaya Mutasi TOS')
                    ->numeric()
                    ->money('idr')
                    ->sortable(),
                Tables\Columns\TextColumn::make('ppn')
                    ->numeric()
                    ->suffix('%')
                    ->sortable(),
                Tables\Columns\TextColumn::make('pph')
                    ->numeric()
                    ->suffix('%')
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
                Tables\Filters\TrashedFilter::make(),
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
            'index' => Pages\ListParameterBiayas::route('/'),
            'create' => Pages\CreateParameterBiaya::route('/create'),
            'view' => Pages\ViewParameterBiaya::route('/{record}'),
            'edit' => Pages\EditParameterBiaya::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function canViewAny(): bool
    {
        return auth()->user()->isAdmin() || auth()->user()->isSuperAdmin();
    }
}
