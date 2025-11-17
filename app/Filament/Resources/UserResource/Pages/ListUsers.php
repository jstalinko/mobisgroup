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
                $username = Str::slug($faker->userName() . rand(100, 999));

                // 2. Auto generate email
                $email = $username . '@mobisgroup.id';

                // 3. Password
                $password = bcrypt('mobisgroup123');

                // 4. License Key (MOBILICS-XXXX-XXXX-XXXX-XXXX)
                $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
                
                $segments = [];
                for ($i = 0; $i < 4; $i++) {
                    $seg = '';
                    for ($j = 0; $j < 4; $j++) {
                        $seg .= $chars[random_int(0, strlen($chars) - 1)];
                    }
                    $segments[] = $seg;
                }

                $licenseKey = 'MOBILICS-' . implode('-', $segments);

                // 5. Buat user baru
                $user = User::create([
                    'name' => $username,
                    'username' => $username,
                    'email' => $email,
                    'password' => $password,
                    'license_key' => $licenseKey,
                ]);

                // 6. Ambil data plan
                $plan = Plan::find($data['plan_id']);

                // 7. Generate Subscription Code: MOBIS-XXXX-XXXX-XXXX
                $codeParts = [];
                for ($i = 0; $i < 3; $i++) {
                    $p = '';
                    for ($j = 0; $j < 4; $j++) {
                        $p .= $chars[random_int(0, strlen($chars) - 1)];
                    }
                    $codeParts[] = $p;
                }
                $subscriptionCode = 'MOBIS-' . implode('-', $codeParts);

                // 8. Insert ke subscriptions
                Subscription::create([
                    'user_id' => $user->id,
                    'plan_id' => $plan->id,
                    'start_at' => Carbon::now(),
                    'end_at' => Carbon::now()->addMonth(),
                    'status' => 'active',
                    'subscription_code' => $subscriptionCode,
                    'price' => $plan->price,
                ]);

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
