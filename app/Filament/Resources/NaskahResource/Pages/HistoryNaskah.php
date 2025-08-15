<?php

namespace App\Filament\Resources\NaskahResource\Pages;

use App\Filament\Resources\NaskahResource;
use App\Models\Logging;
use App\Models\Naskah;
use Filament\Resources\Pages\Page;
use Filament\Tables;
use Filament\Actions;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Database\Eloquent\Builder;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class HistoryNaskah extends Page implements HasTable
{
    use Tables\Concerns\InteractsWithTable;

    protected static string $resource = NaskahResource::class;

    protected static string $view = 'filament.resources.naskah-resource.pages.history-naskah';

    public int $record;

    public function mount(int $record)
    {
        if (! Naskah::whereKey($record)->exists()) {
            throw new NotFoundHttpException();
        }

        $this->record = $record;
    }

    public function getTitle(): string
    {
        return 'Histori Naskah ke-' . $this->record;
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('back')
                ->label('Kembali')
                ->icon('heroicon-o-arrow-left')
                ->color('info')
                ->url(NaskahResource::getUrl('index'))
                ->requiresConfirmation(false),
        ];
    }

    protected function getTableQuery(): Builder
    {
        return Logging::query()
            ->where('naskah_id', $this->record)
            ->latest();
    }

    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('created_at')
                ->label('Tanggal')
                ->date('j M y H:i:s')
                ->sortable(),
            TextColumn::make('user.name')
                ->label('User'),
            TextColumn::make('description')
                ->label('Deskripsi')
                ->limit(50)
                ->tooltip(fn($record) => $record->description),
            TextColumn::make('ip_address')
                ->label('IP Address'),
            TextColumn::make('user_agent')
                ->label('User Agent')
                ->limit(30),
        ];
    }

    protected function getTableActions(): array
    {
        return [
            Tables\Actions\Action::make('detail')
                ->label('Detail')
                ->icon('heroicon-o-eye')
                ->color('gray')
                ->modalHeading('Detail Histori')
                ->modalSubmitAction(false) // Tidak ada tombol submit
                ->modalContent(function ($record) {
                    return view('filament.resources.naskah-resource.partials.history-detail', [
                        'record' => $record,
                    ]);
                }),
        ];
    }

    protected function getTableFilters(): array
    {
        return [
            Tables\Filters\SelectFilter::make('user_id')
                ->label('User')
                ->options(
                    \App\Models\User::orderBy('name')->pluck('name', 'id')->toArray()
                )
                ->searchable(),
        ];
    }
}
