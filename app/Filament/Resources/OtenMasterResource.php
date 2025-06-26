<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OtenMasterResource\Pages;
use App\Filament\Resources\OtenMasterResource\RelationManagers;
use App\Models\OtenMaster;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OtenMasterResource extends Resource
{
    protected static ?string $model = OtenMaster::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Master Data';
    protected static ?int $navigationSort = 6;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('id_oten')
                    ->maxLength(255),
                Forms\Components\TextInput::make('trax_id')
                    ->maxLength(255),
                Forms\Components\TextInput::make('rek_replace')
                    ->maxLength(255),
                Forms\Components\TextInput::make('notas')
                    ->maxLength(255),
                Forms\Components\TextInput::make('periode')
                    ->maxLength(255),
                Forms\Components\TextInput::make('jenis_transaksi')
                    ->maxLength(255),
                Forms\Components\Textarea::make('nama_nasabah')
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('mitra')
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('juru_bayar')
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('cabang')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('kode_otentifikasi')
                    ->maxLength(255),
                Forms\Components\Textarea::make('user')
                    ->columnSpanFull(),
                Forms\Components\DateTimePicker::make('log_date_time'),
                Forms\Components\Textarea::make('status')
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('status_bank')
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('keterangan')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('created_by')
                    ->maxLength(255),
                Forms\Components\TextInput::make('updated_by')
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id_oten')
                    ->searchable(),
                Tables\Columns\TextColumn::make('trax_id')
                    ->searchable(),
                Tables\Columns\TextColumn::make('rek_replace')
                    ->searchable(),
                Tables\Columns\TextColumn::make('notas')
                    ->searchable(),
                Tables\Columns\TextColumn::make('periode')
                    ->searchable(),
                Tables\Columns\TextColumn::make('jenis_transaksi')
                    ->searchable(),
                Tables\Columns\TextColumn::make('kode_otentifikasi')
                    ->searchable(),
                Tables\Columns\TextColumn::make('log_date_time')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_by')
                    ->searchable(),
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
            'index' => Pages\ListOtenMasters::route('/'),
            'create' => Pages\CreateOtenMaster::route('/create'),
            'view' => Pages\ViewOtenMaster::route('/{record}'),
            'edit' => Pages\EditOtenMaster::route('/{record}/edit'),
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