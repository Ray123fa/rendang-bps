<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Naskah extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'naskahs';

    protected $fillable = [
        'tgl_rilis',
        'tgl_disetujui',
        'pengaju',
        'judul',
        'file',
        'status_bps_kota',
        'status_bps_prov',
    ];

    protected $casts = [
        'tgl_rilis' => 'date',
        'tgl_disetujui' => 'date',
    ];

    // Provinsi
    public function scopeStatusBpsProv(Builder $query, string $status): Builder
    {
        return $query->where('status_bps_prov', $status);
    }

    // Kabkot
    public function scopeStatusBpsKota(Builder $query, string $status): Builder
    {
        return $query->where('status_bps_kota', $status);
    }
}
