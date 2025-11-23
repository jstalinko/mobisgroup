<?php

namespace App\Filament\Resources\UserDeviceResource\Pages;

use App\Filament\Resources\UserDeviceResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListUserDevices extends ListRecords
{
    protected static string $resource = UserDeviceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\Action::make('Revoke All Devices')
                ->action(function () {
                    $this->getTable()->getQuery()->update(['revoked' => true]);
                })
                ->requiresConfirmation()
                ->color('danger')
                ->icon('heroicon-o-x-circle'),
        ];
    }
}
