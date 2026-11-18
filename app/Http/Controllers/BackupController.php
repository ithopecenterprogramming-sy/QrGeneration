<?php

namespace App\Http\Controllers;

 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;
use Throwable;

class BackupController extends Controller
{
    private array $tables = [
        'generation_batches',
        'qr_codes',
        'qr_counters',
        'settings',
    ];

    private string $disk = 'public';

    /*
    |--------------------------------------------------------------------------
    | صفحة النسخ الاحتياطي
    |--------------------------------------------------------------------------
    */

    public function index()
    {
        return view('backup.index');
    }

    /*
    |--------------------------------------------------------------------------
    | إنشاء وتحميل النسخة الاحتياطية
    |--------------------------------------------------------------------------
    */

    public function download()
    {
        try {

            $data = [];

            // استخراج بيانات جميع الجداول
            foreach ($this->tables as $table) {

                $data[$table] = DB::table($table)
                    ->orderBy('id')
                    ->get()
                    ->map(fn ($row) => (array) $row)
                    ->all();
            }

            // استخراج مسارات الصور
            $imagePaths = collect($data['qr_codes'])
                ->pluck('image_path')
                ->filter()
                ->unique()
                ->values()
                ->all();

            // إضافة شعار المركز
            $imagePaths[] = 'branding/logo.png';

            $assets = [];

            // حفظ الصور داخل النسخة الاحتياطية
            foreach (array_unique($imagePaths) as $path) {

                if (!$this->isAllowedAssetPath($path)) {
                    throw new \RuntimeException(
                        'مسار ملف غير مسموح به: ' . $path
                    );
                }

                // الشعار اختياري
                if (!Storage::disk($this->disk)->exists($path)) {

                    if ($path === 'branding/logo.png') {
                        continue;
                    }

                    throw new \RuntimeException(
                        'صورة QR غير موجودة: ' . $path
                    );
                }

                $contents = Storage::disk($this->disk)->get($path);

                $assets[$path] = base64_encode($contents);
            }

            // تكوين النسخة الاحتياطية
            $backup = [
                'application' => 'HMC QR Generator',

                'schema_version' => 1,

                'created_at' => now()->toIso8601String(),

                'tables' => $data,

                'assets' => $assets,
            ];

            $filename = 'HMC-Backup-' .
                now()->format('Y-m-d-His') . '.json';

            // تنزيل الملف
            return response()->streamDownload(
                function () use ($backup) {

                    echo json_encode(
                        $backup,
                        JSON_PRETTY_PRINT |
                        JSON_UNESCAPED_UNICODE |
                        JSON_THROW_ON_ERROR
                    );

                },
                $filename,
                [
                    'Content-Type' => 'application/json; charset=UTF-8',
                    'Cache-Control' => 'no-store, no-cache',
                ]
            );

        } catch (Throwable $e) {

            report($e);

            return back()->withErrors([
                'backup' => 'حدث خطأ أثناء إنشاء النسخة الاحتياطية: ' .
                    $e->getMessage(),
            ]);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | استعادة نسخة احتياطية
    |--------------------------------------------------------------------------
    */

    public function restore(Request $request)
    {
        // التحقق من الملف
        $request->validate([
            'backup_file' => [
                'required',
                'file',
                'max:102400',
                'mimes:json',
            ],

            'confirm_restore' => [
                'required',
                'accepted',
            ],
        ], [
            'backup_file.required' => 'يرجى اختيار ملف النسخة الاحتياطية.',
            'backup_file.max' => 'حجم الملف يجب ألا يتجاوز 100 ميجابايت.',
            'backup_file.mimes' => 'يجب اختيار ملف JSON صحيح.',
            'confirm_restore.accepted' => 'يجب تأكيد عملية الاستعادة.',
        ]);

        /*
        |--------------------------------------------------------------------------
        | قراءة الملف
        |--------------------------------------------------------------------------
        */

        try {

            $backup = json_decode(
                file_get_contents(
                    $request->file('backup_file')->getRealPath()
                ),
                true,
                512,
                JSON_THROW_ON_ERROR
            );

        } catch (Throwable $e) {

            throw ValidationException::withMessages([
                'backup_file' => 'ملف النسخة الاحتياطية تالف أو غير صالح.',
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | التحقق من هوية النسخة
        |--------------------------------------------------------------------------
        */

        if (
            !is_array($backup) ||
            ($backup['application'] ?? null) !== 'HMC QR Generator' ||
            ($backup['schema_version'] ?? null) !== 1 ||
            !isset($backup['tables']) ||
            !is_array($backup['tables']) ||
            !isset($backup['assets']) ||
            !is_array($backup['assets'])
        ) {

            throw ValidationException::withMessages([
                'backup_file' => 'الملف ليس نسخة احتياطية متوافقة مع البرنامج.',
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | التحقق من وجود الجداول
        |--------------------------------------------------------------------------
        */

        foreach ($this->tables as $table) {

            if (
                !isset($backup['tables'][$table]) ||
                !is_array($backup['tables'][$table])
            ) {

                throw ValidationException::withMessages([
                    'backup_file' => "بيانات الجدول {$table} غير موجودة.",
                ]);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | التحقق من بنية الجداول
        |--------------------------------------------------------------------------
        */

        $requiredColumns = [

            'generation_batches' => [
                'id',
                'batch_number',
                'prefix',
                'quantity',
                'first_number',
                'last_number',
                'first_serial',
                'last_serial',
                'created_at',
                'updated_at',
            ],

            'qr_codes' => [
                'id',
                'generation_batch_id',
                'serial_number',
                'code',
                'qr_content',
                'image_path',
                'created_at',
                'updated_at',
            ],

            'qr_counters' => [
                'id',
                'last_number',
            ],

            'settings' => [
                'id',
                'key',
                'value',
                'created_at',
                'updated_at',
            ],
        ];

        foreach ($requiredColumns as $table => $columns) {

            foreach ($backup['tables'][$table] as $row) {

                if (!is_array($row)) {

                    throw ValidationException::withMessages([
                        'backup_file' => 'توجد بيانات غير صالحة داخل النسخة.',
                    ]);
                }

                foreach ($columns as $column) {

                    if (!array_key_exists($column, $row)) {

                        throw ValidationException::withMessages([
                            'backup_file' =>
                                "الحقل {$column} غير موجود في {$table}.",
                        ]);
                    }
                }
            }
        }

        /*
        |--------------------------------------------------------------------------
        | التحقق من سلامة الأرقام والعلاقات
        |--------------------------------------------------------------------------
        */

        $batchIds = [];

        foreach ($backup['tables']['generation_batches'] as $batch) {

            $id = $batch['id'];

            if (isset($batchIds[$id])) {

                throw ValidationException::withMessages([
                    'backup_file' => 'يوجد رقم عملية توليد مكرر.',
                ]);
            }

            $batchIds[$id] = true;
        }

        $codes = [];

        $serials = [];

        $maxNumber = 0;

        $expectedImages = [];

        foreach ($backup['tables']['qr_codes'] as $row) {

            $code = $row['code'];

            $serial = $row['serial_number'];

            $batchId = $row['generation_batch_id'];

            if (
                isset($codes[$code]) ||
                isset($serials[$serial]) ||
                !isset($batchIds[$batchId])
            ) {

                throw ValidationException::withMessages([
                    'backup_file' =>
                        'توجد أرقام مكررة أو علاقة غير صحيحة بين الجداول.',
                ]);
            }

            $codes[$code] = true;

            $serials[$serial] = true;

            $maxNumber = max($maxNumber, (int) $serial);

            if (!preg_match('/^HMC-\d{6,}$/', $code)) {

                throw ValidationException::withMessages([
                    'backup_file' => 'يوجد رقم QR غير متوافق مع نظام الترقيم.',
                ]);
            }

            if ($row['image_path'] !== null) {

                if (!$this->isAllowedAssetPath($row['image_path'])) {

                    throw ValidationException::withMessages([
                        'backup_file' => 'مسار صورة QR غير صالح.',
                    ]);
                }

                $expectedImages[] = $row['image_path'];
            }
        }

        /*
        |--------------------------------------------------------------------------
        | التحقق من عداد الترقيم
        |--------------------------------------------------------------------------
        */

        $counterRows = $backup['tables']['qr_counters'];

        if (
            count($counterRows) !== 1 ||
            (int) $counterRows[0]['id'] !== 1 ||
            (int) $counterRows[0]['last_number'] < $maxNumber
        ) {

            throw ValidationException::withMessages([
                'backup_file' => 'عداد الأرقام غير صالح أو أقل من آخر رقم مولّد.',
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | التحقق من الصور
        |--------------------------------------------------------------------------
        */

        $decodedAssets = [];

        foreach ($backup['assets'] as $path => $encoded) {

            if (
                !is_string($path) ||
                !$this->isAllowedAssetPath($path) ||
                !is_string($encoded)
            ) {

                throw ValidationException::withMessages([
                    'backup_file' => 'توجد بيانات ملفات غير صالحة.',
                ]);
            }

            $decoded = base64_decode($encoded, true);

            if ($decoded === false) {

                throw ValidationException::withMessages([
                    'backup_file' => 'إحدى الصور داخل النسخة تالفة.',
                ]);
            }

            // التحقق من أن الملف صورة PNG فعلية
            if (strlen($decoded) < 8 ||
                substr($decoded, 0, 8) !== "\x89PNG\r\n\x1a\n") {

                throw ValidationException::withMessages([
                    'backup_file' => 'أحد الملفات لا يحتوي على صورة PNG صحيحة.',
                ]);
            }

            $decodedAssets[$path] = $decoded;
        }

        foreach (array_unique($expectedImages) as $imagePath) {

            if (!isset($decodedAssets[$imagePath])) {

                throw ValidationException::withMessages([
                    'backup_file' =>
                        'إحدى صور QR المطلوبة غير موجودة داخل النسخة.',
                ]);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | أخذ نسخة مؤقتة من الملفات الحالية لحمايتها عند فشل الاستعادة
        |--------------------------------------------------------------------------
        */

        $disk = Storage::disk($this->disk);

        $oldFiles = [];

        foreach ($disk->allFiles('qr-codes') as $path) {
            $oldFiles[$path] = $disk->get($path);
        }

        if ($disk->exists('branding/logo.png')) {
            $oldFiles['branding/logo.png'] =
                $disk->get('branding/logo.png');
        }

        /*
        |--------------------------------------------------------------------------
        | تنفيذ الاستعادة
        |--------------------------------------------------------------------------
        */

        try {

            DB::transaction(function () use (
                $backup,
                $decodedAssets,
                $disk
            ) {

                // حذف البيانات القديمة
                DB::table('qr_codes')->delete();

                DB::table('generation_batches')->delete();

                DB::table('settings')->delete();

                DB::table('qr_counters')->delete();

                // إدخال البيانات الجديدة
                foreach ($this->tables as $table) {

                    foreach (
                        array_chunk($backup['tables'][$table], 500)
                        as $chunk
                    ) {

                        if (!empty($chunk)) {

                            DB::table($table)->insert($chunk);
                        }
                    }
                }

                // استبدال الصور القديمة بالصور المستوردة
                $disk->deleteDirectory('qr-codes');

                $disk->delete('branding/logo.png');

                foreach ($decodedAssets as $path => $contents) {

                    if (!$disk->put($path, $contents)) {

                        throw new \RuntimeException(
                            'تعذر حفظ ملف: ' . $path
                        );
                    }
                }

            });

        } catch (Throwable $e) {

            report($e);

            // إعادة الملفات القديمة إذا فشلت الاستعادة
            $disk->deleteDirectory('qr-codes');

            $disk->delete('branding/logo.png');

            foreach ($oldFiles as $path => $contents) {
                $disk->put($path, $contents);
            }

            throw ValidationException::withMessages([
                'backup_file' =>
                    'فشلت عملية الاستعادة. تمت محاولة إعادة الملفات السابقة.',
            ]);
        }

        return redirect()
            ->route('backup.index')
            ->with(
                'success',
                'تمت استعادة قاعدة البيانات والصور بنجاح.'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | السماح بمسارات الملفات المعروفة فقط
    |--------------------------------------------------------------------------
    */

    private function isAllowedAssetPath($path): bool
    {
        if (!is_string($path)) {
            return false;
        }

        return preg_match(
            '#^(qr-codes/[A-Za-z0-9._-]+\.png|branding/logo\.png)$#',
            $path
        ) === 1;
    }
}
