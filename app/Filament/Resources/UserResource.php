<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user';
    protected static ?string $navigationGroup = 'User & Roles';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required(),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->required(),
                Forms\Components\DateTimePicker::make('email_verified_at')->default(date('mm/dd/yyyy H:i:s')),
                Forms\Components\TextInput::make('password')
                    ->password()
                    ->required(),
                    Forms\Components\Select::make('roles')->relationship('roles', 'name')->multiple()->preload()->searchable(),
                
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
                Tables\Columns\BadgeColumn::make('roles.name'),
                Tables\Columns\TextColumn::make('activeSubscription.plan.name')->label('Subscription')
                ->formatStateUsing(function($record){
                    return $record->activeSubscription ? $record->activeSubscription->plan->name : 'Tidak ada';
                }),
                Tables\Columns\TextColumn::make('license_key')->copyable(),
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
                Tables\Actions\BulkAction::make('revoke_license_key')
                ->label('Regenerate License Key')
    ->requiresConfirmation()
    ->deselectRecordsAfterCompletion()
    ->action(function ($records) {

        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';

        foreach ($records as $record) {

            // generate: MOBIS-LICENSE-XXXX-XXXX-XXXX
            $parts = [];

            for ($i = 0; $i < 4; $i++) {
                $segment = '';
                for ($j = 0; $j < 4; $j++) {
                    $segment .= $chars[random_int(0, strlen($chars) - 1)];
                }
                $parts[] = $segment;
            }

            $license = 'MOBILICS-' . implode('-', $parts);

            // update license_key di user
            $record->update([
                'license_key' => $license,
            ]);
        }
    })
    ->color('danger')
    ->icon('heroicon-o-key')
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
}
