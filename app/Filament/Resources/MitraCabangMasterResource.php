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
    protected static ?string $navigationGroup = 'Master Data';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Forms\Components\TextInput::make('cabang_id')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->default(function () {
                        // Ambil ID transaksi terakhir
                        $latest = MitraCabangMaster::orderBy('id', 'desc')->first();

                        // Generate nomor urut
                        $sequence = $latest ?
                            (int) str_replace('MC', '', $latest->cabang_id) + 1 :
                            1;

                        return 'MC' . str_pad($sequence, 5, '0', STR_PAD_LEFT);
                    })
                    ->disabled()
                    ->dehydrated()
                    ->columnSpanFull()
                    ->extraInputAttributes(['style' => 'text-align: center;']),
                Forms\Components\Select::make('mitra_id')
                    ->relationship('mitraMaster', 'nama_mitra')
                    ->required()
                    ->searchable()
                    ->preload(),
                Forms\Components\TextInput::make('nama_cabang')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('cabang_id')
                    ->searchable(),
                Tables\Columns\TextColumn::make('mitraMaster.nama_mitra')
                    ->label('Mitra Pusat')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('nama_cabang')
                    ->searchable()
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