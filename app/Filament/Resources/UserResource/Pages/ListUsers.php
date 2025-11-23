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
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

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
                $username = $user->username;

                // 9. Notifikasi Filament
                Notification::make()
                    ->title('License Created Successfully')
                    ->body("User $username telah dibuat dengan Plan {$plan->name}")
                    ->success()
                    ->send();
            }),

        ];
    }
}
