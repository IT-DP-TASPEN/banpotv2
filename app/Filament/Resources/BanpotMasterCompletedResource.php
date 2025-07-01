<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use App\Models\BanpotMasterCompleted;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\BanpotMasterCompletedResource\Pages;
use App\Filament\Resources\BanpotMasterCompletedResource\RelationManagers;

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
                    ->columnSpanFull()
                    ->visible(function () {
                        $user = auth()->user();
                        return $user->isAdmin() || $user->isSuperAdmin() || $user->isStaffBankDPTaspen() || $user->isApprovalBankDPTaspen();
                    }),
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
                    ->label('Tanggal Realisasi')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tmt_kredit')
                    ->label('Tanggal Jatuh Tempo')
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
                Tables\Columns\TextColumn::make('keterangan'),
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
                Tables\Actions\EditAction::make()
                    ->visible(function () {
                        $user = auth()->user();
                        return $user->isAdmin() || $user->isSuperAdmin() || $user->isStaffBankDPTaspen() || $user->isApprovalBankDPTaspen();
                    }),
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
                    ->visible(function () {
                        $user = auth()->user();
                        return $user->isAdmin() || $user->isSuperAdmin() || $user->isStaffBankDPTaspen() || $user->isApprovalBankDPTaspen();
                    }),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    protected function getListeners(): array
    {
        return [
            'refreshTable' => '$refresh',
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
        return auth()->user()->isAdmin() || auth()->user()->isSuperAdmin() || auth()->user()->isApprovalBankDPTaspen() || auth()->user()->isStaffBankDPTaspen() || auth()->user()->isApprovalMitraPusat() || auth()->user()->isStaffMitraPusat();
    }
}
