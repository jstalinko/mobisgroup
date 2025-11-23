<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RekeningResource\Pages;
use App\Filament\Resources\RekeningResource\RelationManagers;
use App\Models\Rekening;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class RekeningResource extends Resource
{
    protected static ?string $model = Rekening::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $navigationGroup = 'Settings';

public static function form(Form $form): Form
{
    return $form
        ->schema([
            Forms\Components\Select::make('type')
                ->options([
                    'bank' => 'Bank',
                    'ewallet' => 'E-Wallet',
                    'qris' => 'QRIS',
                ])
                ->required()
                ->reactive(),

            Forms\Components\TextInput::make('account_name')
                ->visible(fn ($get) => $get('type') !== 'qris'),

            Forms\Components\TextInput::make('number')
                ->visible(fn ($get) => $get('type') !== 'qris'),

            Forms\Components\FileUpload::make('qrcode_image')
                ->image()
                ->visible(fn ($get) => $get('type') === 'qris'),

            Forms\Components\TextInput::make('bank_name')
                ->visible(fn ($get) => $get('type') !== 'qris'),

            Forms\Components\Toggle::make('active')
                ->required(),
        ]);
}


    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('type')
                    ->searchable(),
                Tables\Columns\TextColumn::make('account_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('number')
                    ->searchable(),
                Tables\Columns\ImageColumn::make('qrcode_image'),
                Tables\Columns\TextColumn::make('bank_name')
                    ->searchable(),
                Tables\Columns\IconColumn::make('active')
                    ->boolean(),
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
            'index' => Pages\ListRekenings::route('/'),
            'create' => Pages\CreateRekening::route('/create'),
            'view' => Pages\ViewRekening::route('/{record}'),
            'edit' => Pages\EditRekening::route('/{record}/edit'),
        ];
    }
}
