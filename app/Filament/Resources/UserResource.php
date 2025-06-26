<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\MitraCabang;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\UserResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\MitraCabangMaster;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Master Data';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->required()
                    ->maxLength(255),
                Forms\Components\DateTimePicker::make('email_verified_at')
                    ->default(now()),
                Forms\Components\TextInput::make('password')
                    ->password()
                    ->maxLength(255)
                    ->dehydrateStateUsing(function ($state, $context) {
                        if (filled($state)) {
                            return bcrypt($state);
                        }
                        if ($context === 'edit') {
                            return fn($record) => $record->password;
                        }
                        return null;
                    })
                    ->required(fn($context) => $context === 'create')
                    ->label('Password')
                    ->helperText('Kosongkan jika tidak ingin mengubah password')
                    ->dehydrated(fn($state) => filled($state)),
                Forms\Components\Select::make('roles')
                    ->options([
                        '0' => 'Super Admin',
                        '1' => 'Admin',
                        '2' => 'Approval Bank dp taspen',
                        '3' => 'Staff Bank dp taspen',
                        '4' => 'Approval Mitra Pusat',
                        '5' => 'Approval Mitra Cabang',
                        '6' => 'Staff Mitra Pusat',
                        '7' => 'Staff Mitra Cabang',
                    ])
                    ->required(),
                Forms\Components\Select::make('mitra_id')
                    ->relationship('mitraMaster', 'nama_mitra')
                    ->nullable()
                    ->searchable()
                    ->preload()
                    ->live(),

                Forms\Components\Select::make('mitra_cabang_id')
                    ->options(function (Forms\Get $get) {
                        $mitraId = $get('mitra_id');

                        if (!$mitraId) {
                            return [];
                        }

                        return MitraCabangMaster::where('mitra_id', $mitraId)
                            ->pluck('nama_cabang', 'id');
                    })
                    ->nullable()
                    ->searchable()
                    ->preload()
                    ->disabled(fn(Forms\Get $get): bool => !$get('mitra_id')),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email_verified_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('roles')
                    ->label('Role')
                    ->formatStateUsing(function ($state) {
                        $roles = [
                            '0' => 'Super Admin',
                            '1' => 'Admin',
                            '2' => 'Approval Bank dp taspen',
                            '3' => 'Staff Bank dp taspen',
                            '4' => 'Approval Mitra Pusat',
                            '5' => 'Approval Mitra Cabang',
                            '6' => 'Staff Mitra Pusat',
                            '7' => 'Staff Mitra Cabang',
                        ];
                        return $roles[$state] ?? $state;
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('mitraMaster.nama_mitra')
                    ->label('Mitra Pusat')
                    ->searchable(),
                Tables\Columns\TextColumn::make('mitraCabang.nama_cabang')
                    ->label('Mitra Cabang')
                    ->searchable(),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'view' => Pages\ViewUser::route('/{record}'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()->isAdmin() || auth()->user()->isSuperAdmin();
    }
}