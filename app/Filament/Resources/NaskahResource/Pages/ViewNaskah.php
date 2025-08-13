<?php

namespace App\Filament\Resources\NaskahResource\Pages;

use App\Filament\Resources\NaskahResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewNaskah extends ViewRecord
{
    protected static string $resource = NaskahResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('setujui')
                ->label('Setujui')
                ->color('success')
                ->icon('heroicon-o-check')
                ->requiresConfirmation()
                ->modalHeading('Konfirmasi Persetujuan')
                ->modalDescription('Apakah Anda yakin ingin menyetujui naskah ini?')
                ->modalSubmitActionLabel('Ya, Setujui')
                ->action(function () {
                    $this->record->update([
                        'status_bps_prov' => 'Disetujui', // ganti sesuai kolom di tabel kamu
                        'tgl_disetujui' => now(), // Set tanggal disetujui ke tanggal sekarang
                    ]);
                })
                ->visible(fn() => $this->record->status_bps_prov === 'Belum Ditanggapi' && auth()->user()->hasRole('super_admin')),
            Actions\Action::make('tolak')
                ->label('Tolak')
                ->color('danger')
                ->icon('heroicon-o-x-mark')
                ->requiresConfirmation()
                ->modalHeading('Konfirmasi Penolakan')
                ->modalDescription(fn($record) => 'Apakah Anda yakin ingin menolak naskah "' . $record->judul . '"?')
                ->modalSubmitActionLabel('Ya, Tolak')
                ->form([
                    \Filament\Forms\Components\Textarea::make('keterangan')
                        ->required()
                        ->placeholder('Masukkan keterangan penolakan... (255 karakter maksimal)')
                        ->rows(3),
                ])
                ->action(function () {
                    $this->record->update([
                        'status_bps_prov' => 'Ditolak', // ganti sesuai kolom di tabel kamu
                    ]);
                })
                ->visible(fn() => $this->record->status_bps_prov === 'Belum Ditanggapi' && auth()->user()->hasRole('super_admin')),
            Actions\Action::make('rilis')
                ->label('Rilis')
                ->color('gray')
                ->icon('heroicon-o-megaphone')
                ->requiresConfirmation()
                ->modalHeading('Rilis Naskah')
                ->modalDescription('Apakah Anda yakin ingin rilis naskah ini?')
                ->modalSubmitActionLabel('Ya, Rilis')
                ->action(function () {
                    $this->record->update([
                        'status_bps_kota' => 'Rilis', // ganti sesuai kolom di tabel kamu
                        'tgl_rilis' => now(), // Set tanggal rilis ke tanggal sekarang
                    ]);
                })
                // ->visible(fn() => $this->record->status_bps_kota === 'Terkirim' && auth()->user()->hasRole('kabkot')),
                ->visible(function () {
                    $condition = $this->record->status_bps_kota === 'Terkirim' && auth()->user()->hasRole('kabkot') && $this->record->status_bps_prov === 'Disetujui';
                    return $condition;
                }),
            Actions\EditAction::make()
                ->visible(fn() => auth()->user()->hasRole('kabkot')),
        ];
    }
}
