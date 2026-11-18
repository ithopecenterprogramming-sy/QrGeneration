
@extends('layouts.app')

@section('title', 'توليد رموز جديدة')

@section('content')

<h3 class="fw-bold mb-4">توليد رموز QR جديدة</h3>

<div class="content-card" style="max-width:650px">

    <h5 class="fw-bold mb-3">إعدادات التوليد</h5>

    <form method="POST" action="{{ route('qr.store') }}">
        @csrf

        <label class="form-label fw-bold">
            عدد الرموز المطلوب توليدها
        </label>

        <input
            type="number"
            name="quantity"
            class="form-control form-control-lg"
            value="{{ old('quantity', 40) }}"
            min="1"
            max="1000"
            required
        >

        <div class="mt-3 mb-4 text-secondary">
            <div>البادئة: HMC</div>
            <div>مثال: HMC-000001</div>
            <div>محتوى QR: الرقم التسلسلي فقط</div>
        </div>

        <div class="alert alert-warning">
            سيتم إنشاء أرقام جديدة فقط، ولن تتم إعادة استخدام
            الأرقام التي سبق توليدها.
        </div>

        <button class="btn btn-gold btn-lg px-5">
            توليد الرموز
        </button>

    </form>

</div>

@endsection
