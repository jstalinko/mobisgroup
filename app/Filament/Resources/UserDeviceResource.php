<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserDeviceResource\Pages;
use App\Filament\Resources\UserDeviceResource\RelationManagers;
use App\Models\UserDevice;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UserDeviceResource extends Resource
{
    protected static ?string $model = UserDevice::class;

    protected static ?string $navigationIcon = 'heroicon-o-device-phone-mobile';

    public static function canCreate(): bool
    {
        return false;
    }
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('user_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('device_id')
                    ->required(),
                Forms\Components\TextInput::make('device_name'),
                Forms\Components\TextInput::make('user_agent'),
                Forms\Components\TextInput::make('ip'),
                Forms\Components\DateTimePicker::make('last_active'),
                Forms\Components\Toggle::make('revoked')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('device_id')
                    ->searchable(),
                Tables\Columns\TextColumn::make('device_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('user_agent')
    ->limit(40) // potong
    ->tooltip(fn ($record) => $record->user_agent)->searchable(),
                Tables\Columns\TextColumn::make('ip')
                    ->searchable(),
                Tables\Columns\TextColumn::make('last_active')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\ToggleColumn::make('revoked'),
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
            'index' => Pages\ListUserDevices::route('/'),
            'create' => Pages\CreateUserDevice::route('/create'),
            'view' => Pages\ViewUserDevice::route('/{record}'),
            'edit' => Pages\EditUserDevice::route('/{record}/edit'),
        ];
    }
}
