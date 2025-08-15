<?php

namespace App\Filament\Resources\NaskahResource\Pages;

use App\Filament\Resources\NaskahResource;
use App\Models\Logging;
use Filament\Actions;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
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
                        'status_bps_kota' => 'Menunggu Rilis',
                        'tgl_disetujui' => now(), // Set tanggal disetujui ke tanggal sekarang
                    ]);

                    // Logging
                    Logging::create([
                        'user_id'     => auth()->id(),
                        'naskah_id'   => $this->record->id,
                        'model'       => 'naskah',
                        'description' => 'Menyetujui naskah dengan judul: ' . $this->record->judul . ' yang diajukan oleh ' . $this->record->pengaju,
                        'ip_address'  => request()->ip(),
                        'user_agent'  => request()->userAgent(),
                    ]);

                    // Kirim notifikasi ke superadmin
                    Notification::make()
                        ->title('Naskah Disetujui')
                        ->success()
                        ->send();

                    // Refresh halaman
                    $this->redirect($this->getResource()::getUrl('view', ['record' => $this->record]));
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
                        ->rows(3)
                        ->maxLength(255),
                ])
                ->action(function (array $data) {
                    $this->record->update([
                        'status_bps_prov' => 'Ditolak', // ganti sesuai kolom di tabel kamu
                        'status_bps_kota' => 'Perlu Revisi',
                        'keterangan' => $data['keterangan'], // Ambil dari form
                    ]);

                    // Logging
                    Logging::create([
                        'user_id'     => auth()->id(),
                        'naskah_id'   => $this->record->id,
                        'model'       => 'naskah',
                        'description' => 'Menolak naskah "' . $this->record->judul . '" dengan alasan: ' . $data['keterangan'],
                        'ip_address'  => request()->ip(),
                        'user_agent'  => request()->userAgent(),
                    ]);

                    // Kirim notifikasi ke superadmin
                    Notification::make()
                        ->title('Naskah Ditolak')
                        ->success()
                        ->send();

                    // Refresh halaman
                    $this->redirect($this->getResource()::getUrl('view', ['record' => $this->record]));
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

                    // Logging
                    Logging::create([
                        'user_id'     => auth()->id(),
                        'naskah_id'   => $this->record->id,
                        'model'       => 'naskah',
                        'description' => 'Merilis naskah dengan judul "' . $this->record->judul . '"',
                        'ip_address'  => request()->ip(),
                        'user_agent'  => request()->userAgent(),
                    ]);

                    // Kirim notifikasi ke superadmin
                    Notification::make()
                        ->title('Naskah Dirilis')
                        ->success()
                        ->send();

                    // Refresh halaman
                    $this->redirect($this->getResource()::getUrl('view', ['record' => $this->record]));
                })
                ->visible(function () {
                    $condition = auth()->user()->hasRole('kabkot') && $this->record->status_bps_kota === 'Menunggu Rilis' &&  $this->record->status_bps_prov === 'Disetujui';
                    return $condition;
                }),
            Actions\Action::make('kirimRevisi')
                ->label('Kirim Revisi')
                ->color('gray')
                ->icon('heroicon-o-arrow-path')
                ->form([
                    FileUpload::make('file_revisi')
                        ->label('Unggah File Revisi')
                        ->required()
                        ->directory('naskah')
                        ->preserveFilenames()
                        ->downloadable()
                        ->openable()
                        ->acceptedFileTypes([
                            'image/*', // semua format gambar
                            'application/pdf',
                            'application/vnd.openxmlformats-officedocument.wordprocessingml.document', // docx
                            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', // xlsx
                            'application/vnd.openxmlformats-officedocument.presentationml.presentation', // pptx
                        ]),
                ])
                ->action(function (array $data) {
                    $this->record->update([
                        'file' => $data['file_revisi'],
                        'keterangan' => null,
                        'status_bps_kota' => 'Revisi Diajukan',
                        'status_bps_prov' => 'Belum Ditanggapi'
                    ]);

                    // Logging
                    Logging::create([
                        'user_id'     => auth()->id(),
                        'naskah_id'   => $this->record->id,
                        'model'       => 'naskah',
                        'description' => 'Mengirimkan revisi naskah dengan judul "' . $this->record->judul . '"',
                        'ip_address'  => request()->ip(),
                        'user_agent'  => request()->userAgent(),
                    ]);

                    // Kirim notifikasi ke superadmin
                    Notification::make()
                        ->title('Revisi Dikirim')
                        ->success()
                        ->send();

                    // Refresh halaman
                    $this->redirect($this->getResource()::getUrl('view', ['record' => $this->record]));
                })
                ->visible(function () {
                    $condition = auth()->user()->hasRole('kabkot') && $this->record->status_bps_kota === 'Perlu Revisi' &&  $this->record->status_bps_prov === 'Ditolak';
                    return $condition;
                }),
            Actions\EditAction::make()
                ->visible(fn() => auth()->user()->hasRole('kabkot')),
            Actions\DeleteAction::make()
                ->label('Hapus')
                ->modalHeading('Hapus Naskah')
                ->modalDescription(fn($record) => 'Apakah Anda yakin ingin menghapus naskah "' . $record->judul . '"?')
                ->modalSubmitActionLabel('Ya, Hapus')
                ->after(function ($record) {
                    Logging::create([
                        'user_id'     => auth()->id(),
                        'naskah_id'   => $this->record->id,
                        'model'       => 'naskah',
                        'description' => 'Menghapus naskah "' . $record->judul . '"',
                        'ip_address'  => request()->ip(),
                        'user_agent'  => request()->userAgent(),
                    ]);
                })
                ->visible(fn() => auth()->user()->hasRole('kabkot')),
            Actions\Action::make('history')
                ->label('Lihat History')
                ->color('gray')
                ->icon('heroicon-o-clock')
                ->url(fn() => static::getResource()::getUrl('history', ['record' => $this->record])),
        ];
    }
}
