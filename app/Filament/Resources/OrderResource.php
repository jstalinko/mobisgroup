<?php

namespace App\Filament\Resources;

use BezhanSalleh\FilamentShield\Traits\HasPanelShield;
use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use App\Models\Order;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\Subscription;
use Filament\Resources\Resource;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use App\Filament\Resources\OrderResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\OrderResource\RelationManagers;

class OrderResource extends Resource
{
    use HasPanelShield;
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required(),
                Forms\Components\Select::make('plan_id')
                    ->required()
                    ->relationship('plan', 'name'),
                Forms\Components\TextInput::make('invoice')
                    ->required(),
                Forms\Components\TextInput::make('total_amount')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('price')
                    ->required()
                    ->numeric()
                    ->prefix('Rp'),
                Forms\Components\Select::make('status')
                    ->required()->options([
                        'pending' => 'Pending',
                        'paid' => 'Paid',
                        'cancelled' => 'Cancelled',
                    ]),
                Forms\Components\TextInput::make('payment_method'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->sortable(),
                Tables\Columns\TextColumn::make('plan.name')
                    ->sortable(),
                Tables\Columns\TextColumn::make('invoice')
                    ->searchable(),
                Tables\Columns\TextColumn::make('total_amount')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('price')
                    ->money('IDR')
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->searchable()
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'paid',
                        'danger' => 'cancelled',
                    ]),
                Tables\Columns\TextColumn::make('payment_method')
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
        Tables\Actions\BulkAction::make('Mark as Paid')
            ->action(function (Collection $records) {
                $message = "";
               // dd($records);
                
                foreach($records as $order) {
                    if($order->status === 'paid') {
                        $message .= "Order ID: " . $order->id . " is already marked as paid.\n";
                        continue;
                    }
                    // Update status to paid
                    $order->update(['status' => 'paid']);
                    
                    $plan_id = $order->plan_id;
                    $user_id = $order->user_id ?? null;
                    
                    if($user_id == null) {
                        $user = User::makeRandomuser();
                        $order->update(['user_id' => $user->id]);
                    } else {
                        $user = User::find($user_id);
                    }
                    $user->regenerateLicenseKey();
                    $sub = Subscription::makeSubscription($user->id, $plan_id, $order->price);
                    $message .= "Created subscription ID: " . $sub->id . " for Order ID: " . $order->id . "\n";

                    if($order->referral_code){
                        $plan = \App\Models\Plan::find($plan_id);
                        $refUser = User::where('name',$order->referral_code)->first();
                        if($refUser){
                            $referral = new \App\Models\Referral();
                            $referral->user_id = $refUser->id;
                            $referral->referred_user_id = $user->id;
                            $referral->bonus_amount = $plan->referral_commission;
                            $referral->plan_id = $plan_id;
                            $referral->status = 'completed';
                            $referral->notes = 'Referral bonus order Plan ' . $plan->name;
                            $referral->save();
                        }
                    }
                }

                // Show notification
                Notification::make()
                    ->title('Orders Updated Successfully')
                    ->body($message)
                    ->success()
                    ->send();
            })
            ->deselectRecordsAfterCompletion()
            ->requiresConfirmation()
            ->color('success')
            ->icon('heroicon-o-check-circle'),
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
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'view' => Pages\ViewOrder::route('/{record}'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}
