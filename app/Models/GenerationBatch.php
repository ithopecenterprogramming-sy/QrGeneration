<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GenerationBatch extends Model
{
    protected $fillable = [
        'batch_number',
        'prefix',
        'quantity',
        'first_number',
        'last_number',
        'first_serial',
        'last_serial',
    ];

    public function qrCodes(): HasMany
    {
        return $this->hasMany(QrCode::class);
    }
}