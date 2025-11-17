<?php

namespace App\Filament\Widgets;

use App\Models\Link;
use App\Models\Logs;
use App\Models\User;
use App\Models\Domain;
use Illuminate\Support\Facades\Redis;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class StatOverviewWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $cached = Redis::keys('*');
        return [
            Stat::make('Total Pengguna' , User::count()),
            Stat::make('Cached DB' , count($cached))
        ];
    }
}
