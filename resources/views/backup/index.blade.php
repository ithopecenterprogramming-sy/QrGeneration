
@extends('layouts.app')

@section('title', 'النسخ الاحتياطي والاستعادة')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">

    <div>
        <h3 class="fw-bold mb-1">النسخ الاحتياطي والاستعادة</h3>

        <p class="text-secondary mb-0">
            إدارة النسخ الاحتياطية لبرنامج HMC QR Generator
        </p>
    </div>

</div>


{{-- رسائل النجاح --}}

@if(session('success'))

    <div class="alert alert-success alert-dismissible fade show">

        {{ session('success') }}

        <button type="button"
                class="btn-close"
                data-bs-dismiss="alert">
        </button>

    </div>

@endif


{{-- رسائل الأخطاء --}}

@if($errors->any())

    <div class="alert alert-danger">

        <strong>حدث خطأ:</strong>

        <ul class="mb-0 mt-2">

            @foreach($errors->all() as $error)

                <li>{{ $error }}</li>

            @endforeach

        </ul>

    </div>

@endif


<div class="row g-4">

    {{-- إنشاء نسخة احتياطية --}}

    <div class="col-lg-6">

        <div class="content-card h-100">

            <div class="d-flex align-items-center gap-3 mb-4">

                <div class="rounded-3 p-3"
                     style="background:#F5F0E5;color:#B18A45">

                    <i class="fs-3">⬇</i>

                </div>

                <div>

                    <h5 class="fw-bold mb-1">
                        إنشاء نسخة احتياطية
                    </h5>

                    <small class="text-secondary">
                        تنزيل نسخة كاملة من بيانات البرنامج
                    </small>

                </div>

            </div>


            <p class="text-secondary">
                يمكنك إنشاء نسخة احتياطية وحفظها في أي مكان على جهازك،
                واستخدامها لاحقاً لاستعادة جميع البيانات.
            </p>


            <div class="border rounded-3 p-3 mb-4">

                <div class="fw-bold mb-2">
                    محتويات النسخة الاحتياطية:
                </div>

                <ul class="mb-0 text-secondary">

                    <li>جميع رموز QR وأرقامها التسلسلية.</li>

                    <li>جميع عمليات التوليد السابقة.</li>

                    <li>عداد الترقيم لمنع تكرار الأرقام.</li>

                    <li>إعدادات البرنامج.</li>

                    <li>صور رموز QR.</li>

                    <li>شعار المركز.</li>

                </ul>

            </div>


            <div class="alert alert-info">

                سيتم تنزيل ملف بصيغة JSON يحتوي على البيانات والصور.

            </div>


            <a href="{{ route('backup.download') }}"
               class="btn btn-gold btn-lg w-100">

                <span class="me-2">⬇</span>

                تنزيل نسخة احتياطية الآن

            </a>

        </div>

    </div>


    {{-- استعادة نسخة احتياطية --}}

    <div class="col-lg-6">

        <div class="content-card h-100">

            <div class="d-flex align-items-center gap-3 mb-4">

                <div class="rounded-3 p-3"
                     style="background:#FDEDEC;color:#C0392B">

                    <i class="fs-3">⬆</i>

                </div>

                <div>

                    <h5 class="fw-bold mb-1">
                        استعادة نسخة احتياطية
                    </h5>

                    <small class="text-secondary">
                        استيراد بيانات محفوظة سابقاً
                    </small>

                </div>

            </div>


            <p class="text-secondary">
                اختر ملف النسخة الاحتياطية الذي سبق تنزيله
                لاستعادة البيانات والصور.
            </p>


            <form method="POST"
                  action="{{ route('backup.restore') }}"
                  enctype="multipart/form-data"
                  id="restoreForm">

                @csrf


                <label class="form-label fw-bold">
                    ملف النسخة الاحتياطية
                </label>


                <input
                    type="file"
                    name="backup_file"
                    id="backupFile"
                    class="form-control form-control-lg mb-3"
                    accept=".json,application/json"
                    required
                >


                <div id="selectedFile"
                     class="small text-secondary mb-3">

                    لم يتم اختيار ملف.

                </div>


                <div class="alert alert-danger">

                    <strong>تحذير مهم!</strong>

                    <p class="mb-0 mt-2">

                        عملية الاستعادة ستستبدل جميع البيانات الحالية
                        بالبيانات الموجودة داخل النسخة الاحتياطية.

                        ننصح بإنشاء نسخة احتياطية من الحالة الحالية
                        قبل الاستعادة.

                    </p>

                </div>


                <div class="form-check mb-4">

                    <input
                        class="form-check-input"
                        type="checkbox"
                        name="confirm_restore"
                        value="1"
                        id="confirmRestore"
                        required
                    >

                    <label class="form-check-label fw-bold"
                           for="confirmRestore">

                        أؤكد أنني أفهم أن الاستعادة ستستبدل البيانات الحالية.

                    </label>

                </div>


                <button
                    type="submit"
                    class="btn btn-danger btn-lg w-100"
                    id="restoreButton">

                    <span class="me-2">⬆</span>

                    استعادة النسخة الاحتياطية

                </button>

            </form>

        </div>

    </div>

</div>


{{-- معلومات إضافية --}}

<div class="content-card mt-4">

    <h5 class="fw-bold mb-3">
        إرشادات النسخ الاحتياطي
    </h5>

    <div class="row g-3">

        <div class="col-md-4">

            <div class="border rounded-3 p-3 h-100">

                <strong>حفظ النسخ</strong>

                <p class="text-secondary small mt-2 mb-0">

                    احتفظ بنسخة احتياطية في قرص آخر أو وحدة USB،
                    وليس فقط على القرص الذي يحتوي البرنامج.

                </p>

            </div>

        </div>


        <div class="col-md-4">

            <div class="border rounded-3 p-3 h-100">

                <strong>استعادة البيانات</strong>

                <p class="text-secondary small mt-2 mb-0">

                    تأكد من أن النسخة الاحتياطية تعود إلى
                    إصدار متوافق من البرنامج.

                </p>

            </div>

        </div>


        <div class="col-md-4">

            <div class="border rounded-3 p-3 h-100">

                <strong>حماية الأرقام</strong>

                <p class="text-secondary small mt-2 mb-0">

                    يتم استرجاع عداد الترقيم أيضاً حتى يستمر
                    البرنامج من آخر رقم محفوظ.

                </p>

            </div>

        </div>

    </div>

</div>


@endsection


@push('scripts')

<script>

    const backupInput = document.getElementById('backupFile');

    const selectedFile = document.getElementById('selectedFile');

    const restoreForm = document.getElementById('restoreForm');

    const restoreButton = document.getElementById('restoreButton');


    // عرض اسم الملف المختار

    backupInput.addEventListener('change', function () {

        if (this.files.length > 0) {

            const file = this.files[0];

            selectedFile.textContent =
                'الملف المختار: ' +
                file.name +
                ' - ' +
                (file.size / 1024 / 1024).toFixed(2) +
                ' MB';

        } else {

            selectedFile.textContent = 'لم يتم اختيار ملف.';

        }

    });


    // تأكيد الاستعادة

    restoreForm.addEventListener('submit', function (event) {

        const confirmed = confirm(
            'تحذير!\n\n' +
            'سيتم استبدال جميع بيانات البرنامج الحالية.\n\n' +
            'هل أنشأت نسخة احتياطية من بياناتك الحالية؟\n\n' +
            'هل تريد متابعة الاستعادة؟'
        );

        if (!confirmed) {

            event.preventDefault();

            return;
        }

        restoreButton.disabled = true;

        restoreButton.textContent = 'جاري استعادة البيانات...';

    });

</script>

@endpush
