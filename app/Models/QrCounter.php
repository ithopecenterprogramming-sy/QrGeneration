<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QrCounter extends Model
{
    protected $table = 'qr_counters';

    public $timestamps = false;

    protected $fillable = [
        'last_number',
    ];
}