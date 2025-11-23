<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SubscriptionResource\Pages;
use App\Filament\Resources\SubscriptionResource\RelationManagers;
use App\Models\Subscription;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SubscriptionResource extends Resource
{
    protected static ?string $model = Subscription::class;

    protected static ?string $navigationIcon = 'heroicon-o-bell-alert';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->required()
                    ->relationship('user','name'),
                Forms\Components\Select::make('plan_id')
                    ->required()
                    ->relationship('plan','name'),
                Forms\Components\DatePicker::make('start_at')
                    ->required(),
                Forms\Components\DatePicker::make('end_at')
                    ->required(),
                Forms\Components\Select::make('status')
                    ->required()
                    ->options([
                        'active' =>'Active',
                        'expired' => 'Expired',
                        'canceled' => 'Canceled',
                        'inactive' => 'Inactive'
                    ]),
            
Forms\Components\TextInput::make('subscription_code')
    ->label('Subscription Code')
    ->hint('Klik ikon untuk generate kode')
    ->suffixAction(
        Forms\Components\Actions\Action::make('generate_subscription_code')
            ->icon('heroicon-o-sparkles') // ganti icon sesuai kebutuhan
            ->tooltip('Generate subscription code')
            ->action(function ( $set) {
                // generator: MOBIS-XXXX-XXXX-XXXX (X = A-Z0-9)
                $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
                $parts = [];

                for ($i = 0; $i < 3; $i++) {
                    $part = '';
                    for ($j = 0; $j < 4; $j++) {
                        $part .= $chars[random_int(0, strlen($chars) - 1)];
                    }
                    $parts[] = $part;
                }

                $code = 'MOBIS-' . implode('-', $parts);

                // set nilai ke field subscription_code
                $set('subscription_code', $code);
            })
    ),
                Forms\Components\TextInput::make('price')
                    ->required()
                    ->numeric()
                    ->prefix('Rp'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('plan.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('start_at')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('end_at')
                    ->date()
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->searchable()
                    ->colors([
                        'success' => 'active',
                        'danger' => 'expired',
                        'warning' => 'canceled',
                        'secondary' => 'inactive',
                    ]),
                Tables\Columns\TextColumn::make('subscription_code')
                    ->searchable(),
                Tables\Columns\TextColumn::make('price')
                    ->money('IDR')
                    ->sortable(),
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
            'index' => Pages\ListSubscriptions::route('/'),
            'create' => Pages\CreateSubscription::route('/create'),
            'view' => Pages\ViewSubscription::route('/{record}'),
            'edit' => Pages\EditSubscription::route('/{record}/edit'),
        ];
    }
}
