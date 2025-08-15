<?php

namespace App\Filament\Resources\NaskahResource\Pages;

use App\Filament\Resources\NaskahResource;
use App\Models\Logging;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateNaskah extends CreateRecord
{
    protected static string $resource = NaskahResource::class;

    protected function afterCreate(): void
    {
        Logging::create([
            'user_id'     => auth()->id(),
            'naskah_id'   => $this->record->id,
            'model'       => 'naskah',
            'description' => 'Membuat naskah baru dengan judul "' . $this->record->judul . '"',
            'ip_address'  => request()->ip(),
            'user_agent'  => request()->userAgent(),
        ]);
    }
}
