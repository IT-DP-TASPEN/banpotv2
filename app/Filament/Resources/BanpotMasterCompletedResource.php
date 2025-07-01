<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BanpotMasterCompletedResource\Pages;
use App\Filament\Resources\BanpotMasterCompletedResource\RelationManagers;
use App\Models\BanpotMasterCompleted;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BanpotMasterCompletedResource extends Resource
{
    protected static ?string $model = BanpotMasterCompleted::class;

    protected static ?string $navigationIcon = 'heroicon-o-document';
    protected static ?string $navigationGroup = 'Banpot';
    protected static ?string $navigationLabel = 'Bantuan Potong Final Status';
    protected static ?int $navigationSort = 10;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('banpot_id')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('rek_tabungan')
                    ->maxLength(255),
                Forms\Components\TextInput::make('nama_nasabah')
                    ->maxLength(255),
                Forms\Components\TextInput::make('notas')
                    ->maxLength(255),
                Forms\Components\TextInput::make('rek_kredit')
                    ->maxLength(255),
                Forms\Components\TextInput::make('tenor')
                    ->maxLength(255),
                Forms\Components\TextInput::make('angsuran_ke')
                    ->maxLength(255),
                Forms\Components\DatePicker::make('tat_kredit'),
                Forms\Components\DatePicker::make('tmt_kredit'),
                Forms\Components\TextInput::make('gaji_pensiun')
                    ->numeric(),
                Forms\Components\TextInput::make('nominal_potongan')
                    ->numeric(),
                Forms\Components\TextInput::make('saldo_mengendap')
                    ->numeric(),
                Forms\Components\TextInput::make('jumlah_tertagih')
                    ->numeric(),
                Forms\Components\TextInput::make('pinbuk_sisa_gaji')
                    ->numeric(),
                Forms\Components\TextInput::make('saldo_after_pinbuk')
                    ->required()
                    ->numeric()
                    ->default(0.00),
                Forms\Components\TextInput::make('bank_transfer')
                    ->maxLength(255),
                Forms\Components\TextInput::make('rek_transfer')
                    ->maxLength(255),
                Forms\Components\TextInput::make('status_banpot')
                    ->required(),
                Forms\Components\Textarea::make('keterangan')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('banpot_id')
                    ->searchable(),
                Tables\Columns\TextColumn::make('rek_tabungan')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nama_nasabah')
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
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tmt_kredit')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('gaji_pensiun')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('nominal_potongan')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('saldo_mengendap')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('jumlah_tertagih')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('pinbuk_sisa_gaji')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('saldo_after_pinbuk')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('bank_transfer')
                    ->searchable(),
                Tables\Columns\TextColumn::make('rek_transfer')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status_banpot'),
                Tables\Columns\TextColumn::make('fee_banpot')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\IconColumn::make('rek_tabungan_valid')
                    ->boolean(),
                Tables\Columns\IconColumn::make('notas_valid')
                    ->boolean(),
                Tables\Columns\IconColumn::make('dapem_valid')
                    ->boolean(),
                Tables\Columns\IconColumn::make('oten_valid')
                    ->boolean(),
                Tables\Columns\IconColumn::make('enrollment_valid')
                    ->boolean(),
                Tables\Columns\TextColumn::make('final_validasi_status')
                    ->badge()
                    ->separator(',')
                    ->listWithLineBreaks(),
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
            'index' => Pages\ListBanpotMasterCompleteds::route('/'),
            'create' => Pages\CreateBanpotMasterCompleted::route('/create'),
            'view' => Pages\ViewBanpotMasterCompleted::route('/{record}'),
            'edit' => Pages\EditBanpotMasterCompleted::route('/{record}/edit'),
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
        return auth()->user()->isAdmin() || auth()->user()->isSuperAdmin() || auth()->user()->isApprovalMitraPusat() || auth()->user()->isStaffMitraPusat();
    }
}
