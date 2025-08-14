<?php

namespace App\Models;

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
        'keterangan',
    ];

    protected $casts = [
        'tgl_rilis' => 'date',
        'tgl_disetujui' => 'date',
    ];
}
