<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Archive extends Model
{
    protected $table = 'archives';

    protected $fillable = [
        'naskah_id',
        'file',
        'keterangan'
    ];
}
