<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MitraCabangMasterResource\Pages;
use App\Filament\Resources\MitraCabangMasterResource\RelationManagers;
use App\Models\MitraCabangMaster;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class MitraCabangMasterResource extends Resource
{
    protected static ?string $model = MitraCabangMaster::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('mitra_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('cabang_id')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('nama_cabang')
                    ->required()
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
                Tables\Columns\TextColumn::make('mitra_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('cabang_id')
                    ->searchable(),
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
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListMitraCabangMasters::route('/'),
            'create' => Pages\CreateMitraCabangMaster::route('/create'),
            'view' => Pages\ViewMitraCabangMaster::route('/{record}'),
            'edit' => Pages\EditMitraCabangMaster::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()->isAdmin() || auth()->user()->isSuperAdmin();
    }
}
