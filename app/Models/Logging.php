<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Logging extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'naskah_id',
        'model',
        'description',
        'ip_address',
        'user_agent',
    ];

    /**
     * Get the user that owns the logging.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
