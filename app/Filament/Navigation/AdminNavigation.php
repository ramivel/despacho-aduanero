<?php

namespace App\Filament\Navigation;

use App\Enums\NavigationGroupEnum;
use Filament\Navigation\NavigationGroup;

final class AdminNavigation
{
    public static function groups(): array
    {
        return [
            NavigationGroup::make()
                ->label(NavigationGroupEnum::ADMINISTRACION->value),
            NavigationGroup::make()
                ->label(NavigationGroupEnum::DESPACHOS->value),
            NavigationGroup::make()
                ->label(NavigationGroupEnum::REPORTES->value),
        ];
    }
}
