<?php

namespace App\Filament\Widgets;

use App\Enum\Role;
use App\Models\Patient;
use App\Models\TestRequest;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AdminWidget extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Patients', Patient::count()),
            Stat::make('Total Doctors', User::where('role', Role::DOCTOR->value)->count()),
            Stat::make('Total Tests', TestRequest::count()),
        ];
    }
}
