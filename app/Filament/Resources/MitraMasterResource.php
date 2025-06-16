<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MitraMasterResource\Pages;
use App\Filament\Resources\MitraMasterResource\RelationManagers;
use App\Models\MitraMaster;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class MitraMasterResource extends Resource
{
    protected static ?string $model = MitraMaster::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Master Data';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Fieldset::make()
                    ->schema([
                        Forms\Components\TextInput::make('mitra_id')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->default(function () {
                                // Ambil ID transaksi terakhir
                                $latest = MitraMaster::orderBy('id', 'desc')->first();

                                // Generate nomor urut
                                $sequence = $latest ?
                                    (int) str_replace('M', '', $latest->mitra_id) + 1 :
                                    1;

                                return 'M' . str_pad($sequence, 5, '0', STR_PAD_LEFT);
                            })
                            ->disabled()
                            ->dehydrated()
                            ->columnSpanFull()
                            ->extraInputAttributes(['style' => 'text-align: center;']),
                    ]),

                Forms\Components\TextInput::make('nama_mitra')
                    ->required(),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->searchable(),
                Tables\Columns\TextColumn::make('mitra_id')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nama_mitra')
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
            'index' => Pages\ListMitraMasters::route('/'),
            'create' => Pages\CreateMitraMaster::route('/create'),
            'view' => Pages\ViewMitraMaster::route('/{record}'),
            'edit' => Pages\EditMitraMaster::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ])->whereNotIn('mitra_id', ['0', '00', '000']);
    }

    public static function canViewAny(): bool
    {
        return auth()->user()->isAdmin() || auth()->user()->isSuperAdmin();
    }
}
