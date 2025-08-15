<?php

namespace App\Filament\Resources;

use App\Filament\Resources\HistoryNaskahResource\Pages;
use App\Models\Logging;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions;
use Illuminate\Database\Eloquent\Builder;

class HistoryNaskahResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = Logging::class;

    protected static ?string $navigationIcon = 'heroicon-o-clock';

    protected static ?string $navigationGroup = 'Manajemen Naskah';

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationLabel = 'Seluruh Histori Naskah';

    protected static ?string $pluralModelLabel = 'Seluruh Histori Naskah';

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
            ->where('model', 'naskah')
            ->when(
                auth()->user()->hasRole('superadmin'),
                fn(Builder $query) => $query,
                fn(Builder $query) => $query->where('user_id', auth()->id())
            );
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal')
                    ->date('j M y H:i:s')
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('User'),
                Tables\Columns\TextColumn::make('description')
                    ->label('Deskripsi')
                    ->limit(50)
                    ->tooltip(fn($record) => $record->description),
                Tables\Columns\TextColumn::make('ip_address')
                    ->label('IP Address'),
                Tables\Columns\TextColumn::make('user_agent')
                    ->label('User Agent')
                    ->limit(30),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('user_id')
                    ->label('User')
                    ->options(
                        \App\Models\User::orderBy('name')->pluck('name', 'id')->toArray()
                    )
                    ->searchable(),
            ])
            ->actions([
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
            ]);
    }

    public static function getPermissionPrefixes(): array
    {
        return [
            'view',
            'view_any',
            'create',
            'update',
            'restore',
            'restore_any',
            'delete',
            'delete_any',
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListHistoryNaskahs::route('/'),
        ];
    }
}
