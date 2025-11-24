<?php

namespace App\Filament\Resources\UserResource\Pages;

use Carbon\Carbon;
use App\Models\Plan;
use App\Models\User;
use Filament\Actions;
use Illuminate\Support\Str;
use App\Models\Subscription;
use Filament\Forms\Components\Select;
use App\Filament\Resources\UserResource;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;
 protected function getHeaderWidgets(): array
{
    return [
        \App\Filament\Widgets\FlashMessageWidget::class
    ];
}
    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\Action::make('New License')
                ->label('New License')
                ->icon('heroicon-o-plus-circle')
                ->form([
                    Select::make('plan_id')
                        ->label('Select Plan')
                        ->options(Plan::all()->pluck('name', 'id'))
                        ->required(),
                ])
                ->action(function (array $data) {

                    $faker = fake();

                    // 1. Auto generate username
                    $user = User::makeRandomuser();

                    // 6. Ambil data plan
                    $plan = Plan::find($data['plan_id']);

                    $sub = Subscription::makeSubscription(
                        $user->id,
                        $plan->id,
                        $plan->price
                    );
                    $username = $user->name;
                    // 9. Notifikasi Filament
                    $message = "User $username telah dibuat dengan Plan {$plan->name} | LICENSE KEY: {$user->license_key}";
                    Notification::make()
                        ->title('License Created Successfully')
                        ->body($message)
                        ->success()
                        ->send();
                    session()->put('filament_action_message', $message);

                    return redirect(static::getUrl());
                }),

            Actions\Action::make('Bulk New License')
                ->label('Bulk New License')
                ->icon('heroicon-o-plus-circle')
                ->color('success')
                ->form([
                    Select::make('plan_id')
                        ->label('Select Plan')
                        ->options(Plan::all()->pluck('name', 'id'))
                        ->required(),
                    TextInput::make('quantity')
                        ->label('Quantity')
                        ->default(5)
                        ->required()
                ])->action(function (array $data) {

                    $faker = fake();
                    $message = "";
                    for ($i = 0; $i < $data['quantity']; $i++) {
                        // 1. Auto generate username
                        $user = User::makeRandomuser();

                        // 6. Ambil data plan
                        $plan = Plan::find($data['plan_id']);

                        $sub = Subscription::makeSubscription(
                            $user->id,
                            $plan->id,
                            $plan->price
                        );
                        $username = $user->name;

                        $message .= "User: $username | Plan : {$plan->name} | License Key : $user->license_key \n";
                    }
                    session()->put('filament_action_message',str_replace("\n","<br>",$message));

                    // 9. Notifikasi Filament
                    Notification::make()
                        ->title('Bulk Licenses Created Successfully')
                        ->body($message)
                        ->success()
                        ->send();
                                            return redirect(static::getUrl());

                })

        ];
    }

}
