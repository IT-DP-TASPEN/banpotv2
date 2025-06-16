<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DapemNedMasterResource\Pages;
use App\Filament\Resources\DapemNedMasterResource\RelationManagers;
use App\Models\DapemNedMaster;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DapemNedMasterResource extends Resource
{
    protected static ?string $model = DapemNedMaster::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Master Data';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('notas')
                    ->maxLength(255),
                Forms\Components\Textarea::make('nama_nasabah')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('kantor_bayar')
                    ->maxLength(255),
                Forms\Components\TextInput::make('jiwa')
                    ->maxLength(255),
                Forms\Components\TextInput::make('jenis')
                    ->maxLength(255),
                Forms\Components\TextInput::make('nominal_dapem')
                    ->numeric(),
                Forms\Components\TextInput::make('rek_replace')
                    ->maxLength(255),
                Forms\Components\TextInput::make('bulan_dapem')
                    ->maxLength(255),
                Forms\Components\TextInput::make('code1')
                    ->maxLength(255),
                Forms\Components\TextInput::make('code2')
                    ->maxLength(255),
                Forms\Components\TextInput::make('code3')
                    ->maxLength(255),
                Forms\Components\TextInput::make('code4')
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('notas')
                    ->searchable(),
                Tables\Columns\TextColumn::make('kantor_bayar')
                    ->searchable(),
                Tables\Columns\TextColumn::make('jiwa')
                    ->searchable(),
                Tables\Columns\TextColumn::make('jenis')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nominal_dapem')
                    ->numeric()
                    ->sortable()
                    ->money('idr'),
                Tables\Columns\TextColumn::make('rek_replace')
                    ->searchable(),
                Tables\Columns\TextColumn::make('bulan_dapem')
                    ->searchable(),
                Tables\Columns\TextColumn::make('code2')
                    ->label('Kode Oten')
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
            'index' => Pages\ListDapemNedMasters::route('/'),
            'create' => Pages\CreateDapemNedMaster::route('/create'),
            'view' => Pages\ViewDapemNedMaster::route('/{record}'),
            'edit' => Pages\EditDapemNedMaster::route('/{record}/edit'),
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
