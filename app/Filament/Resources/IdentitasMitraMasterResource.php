<?php

namespace App\Filament\Resources;

use App\Filament\Resources\IdentitasMitraMasterResource\Pages;
use App\Filament\Resources\IdentitasMitraMasterResource\RelationManagers;
use App\Models\IdentitasMitraMaster;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class IdentitasMitraMasterResource extends Resource
{
    protected static ?string $model = IdentitasMitraMaster::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Master Data';
    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('identity_id')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->default(function () {
                        // Ambil ID transaksi terakhir
                        $latest = IdentitasMitraMaster::orderBy('id', 'desc')->first();

                        // Generate nomor urut
                        $sequence = $latest ?
                            (int) str_replace('IM', '', $latest->identity_id) + 1 :
                            1;

                        return 'IM' . str_pad($sequence, 5, '0', STR_PAD_LEFT);
                    })
                    ->disabled()
                    ->dehydrated()
                    ->columnSpanFull()
                    ->extraInputAttributes(['style' => 'text-align: center;']),
                Forms\Components\Select::make('mitra_id')
                    ->relationship('mitraMaster', 'id')
                    ->required()
                    ->getOptionLabelFromRecordUsing(fn($record) => "{$record->mitra_id} - {$record->nama_mitra}"),
                Forms\Components\TextInput::make('notas')
                    ->maxLength(255),
                Forms\Components\TextInput::make('nama_nasabah')
                    ->required(),
                Forms\Components\TextInput::make('rek_tabungan')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('identity_id')
                    ->searchable(),
                Tables\Columns\TextColumn::make('mitraMaster.mitra_id')
                    ->label('ID Mitra')
                    ->sortable(),
                Tables\Columns\TextColumn::make('mitraMaster.nama_mitra')
                    ->label('Nama Mitra')
                    ->sortable(),
                Tables\Columns\TextColumn::make('notas')
                    ->searchable(),
                Tables\Columns\TextColumn::make('rek_tabungan')
                    ->searchable(),
                Tables\Columns\TextColumn::make('rek_replace')
                    ->searchable(),
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
            'index' => Pages\ListIdentitasMitraMasters::route('/'),
            'create' => Pages\CreateIdentitasMitraMaster::route('/create'),
            'view' => Pages\ViewIdentitasMitraMaster::route('/{record}'),
            'edit' => Pages\EditIdentitasMitraMaster::route('/{record}/edit'),
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
