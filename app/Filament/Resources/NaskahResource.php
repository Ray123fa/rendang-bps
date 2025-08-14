<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NaskahResource\Pages;
use App\Models\Naskah;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class NaskahResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = Naskah::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'Manajemen Naskah';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel = 'Naskah';
    protected static ?string $pluralModelLabel = 'Daftar Naskah';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Naskah')
                    ->schema([
                        Forms\Components\TextInput::make('judul')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('pengaju')
                            ->required()
                            ->default(fn() => auth()->user()->name)
                            ->maxLength(255),
                        Forms\Components\FileUpload::make('file')
                            ->required()
                            ->label('File Naskah')
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
                    ->disabled(fn() => !auth()->user()->hasRole('kabkot'))
                    ->columnSpan(1),
                Forms\Components\Grid::make()
                    ->schema([
                        // Section Status (kiri)
                        Forms\Components\Section::make('Status Naskah')
                            ->schema([
                                Forms\Components\Grid::make()
                                    ->schema([
                                        Forms\Components\Select::make('status_bps_kota')
                                            ->label('Status BPS Kota')
                                            ->options([
                                                'Terkirim' => 'Terkirim',
                                                'Perlu Revisi' => 'Perlu Revisi',
                                                'Revisi Diajukan' => 'Revisi Diajukan',
                                                'Menunggu Rilis' => 'Menunggu Rilis',
                                                'Rilis' => 'Rilis',
                                            ])
                                            ->default('Terkirim')
                                            ->required()
                                            ->disabled(fn() => !auth()->user()->hasRole('kabkot'))
                                            ->columnSpan(1),

                                        Forms\Components\Select::make('status_bps_prov')
                                            ->label('Status BPS Provinsi')
                                            ->options([
                                                'Belum Ditanggapi' => 'Belum Ditanggapi',
                                                'Dalam Review' => 'Dalam Review',
                                                'Disetujui' => 'Disetujui',
                                                'Ditolak' => 'Ditolak',
                                            ])
                                            ->default('Belum Ditanggapi')
                                            ->required()
                                            ->disabled(fn() => !auth()->user()->hasRole('super_admin'))
                                            ->columnSpan(1),
                                    ])
                                    ->columns(2)
                            ]),

                        // Section Keterangan (kanan) - hanya muncul jika ditolak
                        Forms\Components\Section::make('Keterangan Penolakan')
                            ->schema([
                                Forms\Components\Textarea::make('keterangan')
                                    ->label('Alasan Penolakan')
                                    ->placeholder('Masukkan alasan penolakan...')
                                    ->rows(3)
                                    ->maxLength(255)
                                    ->columnSpanFull()
                            ])
                            ->visible(fn($get) => $get('status_bps_prov') === 'Ditolak')
                            ->disabled(fn() => !auth()->user()->hasRole('super_admin'))
                    ])
                    ->columnSpan(1) // Membagi layout menjadi 2 kolom
                    ->visible(fn($record) => filled($record?->id))
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Masuk')
                    ->formatStateUsing(fn($state) => Carbon::parse($state)->translatedFormat('j M y'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('tgl_rilis')
                    ->label('Rilis')
                    ->formatStateUsing(fn($state) => Carbon::parse($state)->translatedFormat('j M y'))
                    ->placeholder('-')
                    ->sortable(),
                Tables\Columns\TextColumn::make('tgl_disetujui')
                    ->label('Disetujui')
                    ->formatStateUsing(fn($state) => Carbon::parse($state)->translatedFormat('j M y'))
                    ->placeholder('-')
                    ->sortable(),
                Tables\Columns\TextColumn::make('judul')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('pengaju')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status_bps_kota')
                    ->label('Status BPS Kota')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'Terkirim' => 'info',
                        'Perlu Revisi' => 'warning',
                        'Revisi Diajukan' => 'info',
                        'Menunggu Rilis' => 'info',
                        'Rilis' => 'success',
                    }),
                Tables\Columns\TextColumn::make('status_bps_prov')
                    ->label('Status BPS Provinsi')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'Belum Ditanggapi' => 'gray',
                        'Dalam Review' => 'info',
                        'Disetujui' => 'success',
                        'Ditolak' => 'danger',
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status_bps_kota')
                    ->label('Status BPS Kota')
                    ->options([
                        'Terkirim' => 'Terkirim',
                        'Perlu Revisi' => 'Perlu Revisi',
                        'Revisi Diajukan' => 'Revisi Diajukan',
                        'Menunggu Rilis' => 'Menunggu Rilis',
                        'Rilis' => 'Rilis',
                    ]),
                Tables\Filters\SelectFilter::make('status_bps_prov')
                    ->label('Status BPS Provinsi')
                    ->options([
                        'Belum Ditanggapi' => 'Belum Ditanggapi',
                        'Dalam Review' => 'Dalam Review',
                        'Disetujui' => 'Disetujui',
                        'Ditolak' => 'Ditolak',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('Lihat'),
                // Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->label('Hapus')
                    ->modalHeading('Hapus Naskah')
                    ->modalDescription(fn($record) => 'Apakah Anda yakin ingin menghapus naskah "' . $record->judul . '"?')
                    ->modalSubmitActionLabel('Ya, Hapus'),
                Tables\Actions\RestoreAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
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

    public static function canCreate(): bool
    {
        return !Auth::user()->hasRole('super_admin');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListNaskahs::route('/'),
            'create' => Pages\CreateNaskah::route('/create'),
            'view' => Pages\ViewNaskah::route('/{record}'),
            'edit' => Pages\EditNaskah::route('/{record}/edit'),
        ];
    }
}
