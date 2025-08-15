<?php

namespace App\Filament\Resources\NaskahResource\Pages;

use App\Filament\Resources\NaskahResource;
use App\Models\Logging;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditNaskah extends EditRecord
{
    protected static string $resource = NaskahResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    protected function afterSave(): void
    {
        Logging::create([
            'user_id'     => auth()->id(),
            'naskah_id'   => $this->record->id,
            'model'       => 'naskah',
            'description' => 'Mengedit naskah dengan judul "' . $this->record->judul . '"',
            'ip_address'  => request()->ip(),
            'user_agent'  => request()->userAgent(),
        ]);
    }
}
