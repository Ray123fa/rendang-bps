<?php

namespace App\Filament\Resources\NaskahResource\Widgets;

use App\Models\Naskah;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class NaskahChart extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            BaseWidget\Stat::make('Belum Ditanggapi', Naskah::statusBpsProv('Belum Ditanggapi')->count())
                ->color('gray')
                ->icon('heroicon-o-inbox'),
            BaseWidget\Stat::make('Dalam Review', Naskah::statusBpsProv('Dalam Review')->count())
                ->color('warning')
                ->icon('heroicon-o-clock'),
            BaseWidget\Stat::make('Disetujui', Naskah::statusBpsProv('Disetujui')->count())
                ->color('success')
                ->icon('heroicon-o-check-circle'),
            BaseWidget\Stat::make('Ditolak', Naskah::statusBpsProv('Ditolak')->count())
                ->color('danger')
                ->icon('heroicon-o-x-circle'),
        ];
    }
}
