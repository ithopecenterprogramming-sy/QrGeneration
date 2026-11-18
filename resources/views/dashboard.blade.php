
@extends('layouts.app')

@section('title', 'لوحة التحكم')

@section('content')

<h3 class="fw-bold mb-4">لوحة التحكم</h3>

<div class="row g-3 mb-4">

    <div class="col-md-3">
        <div class="content-card">
            <div class="text-secondary">إجمالي الرموز</div>
            <h2 class="gold mt-2">{{ number_format($totalCodes) }}</h2>
        </div>
    </div>

    <div class="col-md-3">
        <div class="content-card">
            <div class="text-secondary">عمليات التوليد</div>
            <h2 class="gold mt-2">{{ number_format($totalBatches) }}</h2>
        </div>
    </div>

    <div class="col-md-3">
        <div class="content-card">
            <div class="text-secondary">رموز هذا الشهر</div>
            <h2 class="gold mt-2">{{ number_format($monthlyCodes) }}</h2>
        </div>
    </div>

    <div class="col-md-3">
        <div class="content-card">
            <div class="text-secondary">آخر رقم مولّد</div>
            <h5 class="mt-3">{{ $lastCode ?? 'لم يتم التوليد بعد' }}</h5>
        </div>
    </div>

</div>

<div class="content-card mb-4">
    <h5 class="fw-bold mb-3">توليد رموز جديدة</h5>

    <p class="text-secondary">
        أنشئ دفعة جديدة من رموز QR الخاصة بمركز الأمل الطبي.
    </p>

    <a href="{{ route('qr.create') }}" class="btn btn-gold px-4">
        + توليد رموز QR
    </a>
</div>

<div class="content-card">
    <h5 class="fw-bold mb-3">آخر عمليات التوليد</h5>

    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th>رقم العملية</th>
                    <th>العدد</th>
                    <th>من</th>
                    <th>إلى</th>
                    <th>التاريخ</th>
                    <th>الطباعة</th>
                </tr>
            </thead>

            <tbody>
                @forelse($recentBatches as $batch)
                    <tr>
                        <td>{{ $batch->batch_number }}</td>
                        <td>{{ $batch->quantity }}</td>
                        <td>{{ $batch->first_serial }}</td>
                        <td>{{ $batch->last_serial }}</td>
                        <td>{{ $batch->created_at->format('Y-m-d H:i') }}</td>
                        <td>
                            <a href="{{ route('qr.print', $batch) }}"
                               class="btn btn-sm btn-outline-secondary">
                                طباعة
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-secondary">
                            لا توجد عمليات توليد حتى الآن.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
