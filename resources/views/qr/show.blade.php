
@extends('layouts.app')

@section('title', 'تفاصيل الرمز')

@section('content')

<h3 class="fw-bold mb-4">تفاصيل رمز QR</h3>

<div class="content-card text-center" style="max-width:450px">

    <h4 class="gold">{{ $qrCode->code }}</h4>

    @if($qrCode->image_path)
        <img
            src="{{ asset('storage/'.$qrCode->image_path) }}"
            alt="QR"
            style="width:250px;max-width:100%"
        >
    @endif

    <hr>

    <div class="text-secondary">
        محتوى QR
    </div>

    <h5>{{ $qrCode->qr_content }}</h5>

    <div class="text-secondary mt-3">
        تاريخ الإنشاء
    </div>

    <div>{{ $qrCode->created_at->format('Y-m-d H:i:s') }}</div>

    <div class="text-secondary mt-3">
        رقم عملية التوليد
    </div>

    <div>{{ $qrCode->batch->batch_number }}</div>

    <a href="{{ route('qr.index') }}"
       class="btn btn-outline-secondary mt-4">
        العودة للسجل
    </a>

</div>

@endsection
