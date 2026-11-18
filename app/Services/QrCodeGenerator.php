<?php

namespace App\Services;

use App\Models\QrCode;
use App\Models\QrCounter;
use App\Models\GenerationBatch;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use InvalidArgumentException;

class QrCodeGenerator
{
    public function __construct(
        private QrImageService $qrImageService
    ) {}

    public function generate(int $quantity): GenerationBatch
    {
        if ($quantity < 1 || $quantity > 1000) {
            throw new InvalidArgumentException(
                'العدد يجب أن يكون بين 1 و1000.'
            );
        }

        return DB::transaction(function () use ($quantity) {

            $counter = QrCounter::query()
                ->where('id', 1)
                ->lockForUpdate()
                ->firstOrFail();

            $first = $counter->last_number + 1;
            $last = $counter->last_number + $quantity;

            $batch = GenerationBatch::create([
                'batch_number' => 'HMC-BATCH-' .
                    now()->format('YmdHis') . '-' .
                    Str::upper(Str::random(6)),
                'prefix' => 'HMC',
                'quantity' => $quantity,
                'first_number' => $first,
                'last_number' => $last,
                'first_serial' => 'HMC-' .
                    str_pad($first, 6, '0', STR_PAD_LEFT),
                'last_serial' => 'HMC-' .
                    str_pad($last, 6, '0', STR_PAD_LEFT),
            ]);

            for ($number = $first; $number <= $last; $number++) {

                $code = 'HMC-' .
                    str_pad($number, 6, '0', STR_PAD_LEFT);

                $imagePath = $this->qrImageService->generate(
                    $code,
                    $code
                );

                $batch->qrCodes()->create([
                    'serial_number' => $number,
                    'code' => $code,
                    'qr_content' => $code,
                    'image_path' => $imagePath,
                ]);
            }

            $counter->update([
                'last_number' => $last,
            ]);

            return $batch->load('qrCodes');
        });
    }
}