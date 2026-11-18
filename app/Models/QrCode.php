<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QrCode extends Model
{
    protected $fillable = [
        'generation_batch_id',
        'serial_number',
        'code',
        'qr_content',
        'image_path',
    ];

    public function batch(): BelongsTo
    {
        return $this->belongsTo(
            GenerationBatch::class,
            'generation_batch_id'
        );
    }
}