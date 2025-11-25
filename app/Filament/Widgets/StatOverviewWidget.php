<?php

namespace App\Filament\Widgets;

use App\Models\User;
use App\Helpers\Helper;
use App\Models\Subscription;
use App\Models\UserDevice;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class StatOverviewWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $cached = Helper::cacheVideos();
        return [
            Stat::make('Total Pengguna' , User::count()),
            Stat::make('Cached Videos' , $cached),
            Stat::make('Total Devices',UserDevice::count()),
            Stat::make('Total Active Devices',UserDevice::where('revoked',false)->count()),
            Stat::make('Total Subscriptions',User::whereHas('activeSubscription')->count()),
            Stat::make('Total Plans',\App\Models\Plan::count()),
            Stat::make('Total Income','IDR ' .str_replace(',','.',number_format(Subscription::where('status','active')->sum('price')))),
        ];
    }
}
