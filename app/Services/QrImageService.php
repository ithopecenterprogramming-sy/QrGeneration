<?php

namespace App\Services;
 
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\Logo\Logo;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class QrImageService
{
    /**
     * إنشاء صورة QR وحفظها مع شعار المركز في المنتصف.
     */
    public function generate(string $content, string $filename): string
    {
        // مسار شعار المركز
        $logoPath = Storage::disk('public')
            ->path('branding/logo.png');

        // التحقق من وجود الشعار
        $hasLogo = is_file($logoPath);

        // إنشاء QR Code
        $qrCode = new QrCode(
            data: $content,
            encoding: new Encoding('UTF-8'),
            errorCorrectionLevel: ErrorCorrectionLevel::High,
            size: 600,
            margin: 20,
            roundBlockSizeMode: RoundBlockSizeMode::Margin
        );

        // إعداد الشعار إذا كان موجوداً
        $logo = null;

        if ($hasLogo) {
            $logo = new Logo(
                path: $logoPath,
                resizeToWidth: 90,
                resizeToHeight: 90,
                punchoutBackground: true
            );
        }

        // إنشاء الصورة باستخدام PNG Writer
        $writer = new PngWriter();

        $result = $writer->write(
            $qrCode,
            $logo
        );

        // مسار حفظ الصورة
        $path = 'qr-codes/' . $filename . '.png';

        // حفظ الصورة
        $saved = Storage::disk('public')->put(
            $path,
            $result->getString()
        );

        if (!$saved) {
            throw new RuntimeException(
                'تعذر حفظ صورة QR.'
            );
        }

        return $path;
    }
}
