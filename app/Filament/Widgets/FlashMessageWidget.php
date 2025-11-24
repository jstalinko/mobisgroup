<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class FlashMessageWidget extends Widget
{
    protected static string $view = 'filament.reusable-flash-header';
    protected int | string | array $columnSpan = 'full';
}
