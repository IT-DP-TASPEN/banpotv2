<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ParameterFeeBanpotResource\Pages;
use App\Filament\Resources\ParameterFeeBanpotResource\RelationManagers;
use App\Models\ParameterFeeBanpot;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ParameterFeeBanpotResource extends Resource
{
    protected static ?string $model = ParameterFeeBanpot::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Parameter';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('fee_id')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->default(function () {
                        // Ambil ID transaksi terakhir
                        $latest = ParameterFeeBanpot::orderBy('id', 'desc')->first();

                        // Generate nomor urut
                        $sequence = $latest ?
                            (int) str_replace('FM', '', $latest->fee_id) + 1 :
                            1;

                        return 'FM' . str_pad($sequence, 5, '0', STR_PAD_LEFT);
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
                Forms\Components\Select::make('jenis_fee')
                    ->options([
                        '1' => 'Dapem',
                        '2' => 'Tagihan'
                    ])
                    ->required(),
                Forms\Components\TextInput::make('fee_banpot')
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
                        $set('fee_banpot', $formattedValue);
                    })
                    ->rules(['numeric', 'decimal:0,2']),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('fee_id')
                    ->searchable(),
                Tables\Columns\TextColumn::make('mitraMaster.mitra_id')
                    ->label('ID Mitra')
                    ->sortable(),
                Tables\Columns\TextColumn::make('mitraMaster.nama_mitra')
                    ->label('Nama Mitra')
                    ->sortable(),
                Tables\Columns\TextColumn::make('jenis_fee')
                    ->label('Fee dari')
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        '1' => 'Dapem',
                        '2' => 'Tagihan',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('fee_banpot')
                    ->numeric()
                    ->sortable()
                    ->suffix('%'),
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
            'index' => Pages\ListParameterFeeBanpots::route('/'),
            'create' => Pages\CreateParameterFeeBanpot::route('/create'),
            'view' => Pages\ViewParameterFeeBanpot::route('/{record}'),
            'edit' => Pages\EditParameterFeeBanpot::route('/{record}/edit'),
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
