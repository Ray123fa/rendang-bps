<?php

namespace App\Filament\Resources\NaskahResource\Pages;

use App\Filament\Resources\NaskahResource;
use App\Filament\Resources\NaskahResource\Widgets\NaskahChart;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;

class ListNaskahs extends ListRecords
{
    protected static string $resource = NaskahResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Tambah naskah'),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            NaskahChart::class,
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('Semua Naskah'),
            'belum_ditanggapi' => Tab::make('Belum Ditanggapi')
                ->modifyQueryUsing(fn($query) => $query->statusBpsProv('Belum Ditanggapi')),
            'dalam_review' => Tab::make('Dalam Review')
                ->modifyQueryUsing(fn($query) => $query->statusBpsProv('Dalam Review')),
            'disetujui' => Tab::make('Disetujui')
                ->modifyQueryUsing(fn($query) => $query->statusBpsProv('Disetujui')),
            'ditolak' => Tab::make('Ditolak')
                ->modifyQueryUsing(fn($query) => $query->statusBpsProv('Ditolak')),
        ];
    }
}
