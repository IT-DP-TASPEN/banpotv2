<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FlaggingMutasiTifResource\Pages;
use App\Filament\Resources\FlaggingMutasiTifResource\RelationManagers;
use App\Models\FlaggingMutasiTif;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class FlaggingMutasiTifResource extends Resource
{
    protected static ?string $model = FlaggingMutasiTif::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Permintaan';
    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('permintaan_id')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('wilayah')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('nama_nasabah')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('notas')
                    ->maxLength(255),
                Forms\Components\TextInput::make('nik')
                    ->maxLength(255),
                Forms\Components\Textarea::make('tempat_lahir')
                    ->columnSpanFull(),
                Forms\Components\DatePicker::make('tanggal_lahir'),
                Forms\Components\Textarea::make('alamat')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('no_handphone')
                    ->tel()
                    ->maxLength(255),
                Forms\Components\TextInput::make('rek_tabungan')
                    ->maxLength(255),
                Forms\Components\TextInput::make('rek_kredit')
                    ->maxLength(255),
                Forms\Components\DatePicker::make('tat_kredit'),
                Forms\Components\TextInput::make('ktp')
                    ->maxLength(255),
                Forms\Components\TextInput::make('sp_deb_flagging')
                    ->maxLength(255),
                Forms\Components\TextInput::make('foto_tab')
                    ->maxLength(255),
                Forms\Components\TextInput::make('form_pindah_kantor')
                    ->maxLength(255),
                Forms\Components\TextInput::make('status_permintaan')
                    ->required(),
                Forms\Components\Textarea::make('keterangan')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('bukti_hasil')
                    ->maxLength(255),
                Forms\Components\TextInput::make('biaya_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('created_by')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('updated_by')
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
                    ->searchable(),
                Tables\Columns\TextColumn::make('nik')
                    ->searchable(),
                Tables\Columns\TextColumn::make('tanggal_lahir')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('no_handphone')
                    ->searchable(),
                Tables\Columns\TextColumn::make('rek_tabungan')
                    ->searchable(),
                Tables\Columns\TextColumn::make('rek_kredit')
                    ->searchable(),
                Tables\Columns\TextColumn::make('tat_kredit')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('ktp')
                    ->searchable(),
                Tables\Columns\TextColumn::make('sp_deb_flagging')
                    ->searchable(),
                Tables\Columns\TextColumn::make('foto_tab')
                    ->searchable(),
                Tables\Columns\TextColumn::make('form_pindah_kantor')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status_permintaan'),
                Tables\Columns\TextColumn::make('bukti_hasil')
                    ->searchable(),
                Tables\Columns\TextColumn::make('biaya_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_by')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('updated_by')
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
            'index' => Pages\ListFlaggingMutasiTifs::route('/'),
            'create' => Pages\CreateFlaggingMutasiTif::route('/create'),
            'view' => Pages\ViewFlaggingMutasiTif::route('/{record}'),
            'edit' => Pages\EditFlaggingMutasiTif::route('/{record}/edit'),
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
