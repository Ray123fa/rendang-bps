<?php

namespace App\Filament\Resources\NaskahResource\Pages;

use App\Filament\Resources\NaskahResource;
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
}
