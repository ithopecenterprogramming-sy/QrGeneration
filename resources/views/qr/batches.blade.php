
@extends('layouts.app')

@section('title', 'عمليات التوليد')

@section('content')

<h3 class="fw-bold mb-4">عمليات التوليد السابقة</h3>

<div class="content-card">

    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th>رقم العملية</th>
                    <th>عدد الرموز</th>
                    <th>أول رقم</th>
                    <th>آخر رقم</th>
                    <th>تاريخ العملية</th>
                    <th>الإجراء</th>
                </tr>
            </thead>

            <tbody>
                @forelse($batches as $batch)
                    <tr>
                        <td>{{ $batch->batch_number }}</td>
                        <td>{{ $batch->qr_codes_count }}</td>
                        <td>{{ $batch->first_serial }}</td>
                        <td>{{ $batch->last_serial }}</td>
                        <td>{{ $batch->created_at->format('Y-m-d H:i') }}</td>
                        <td>
                            <a href="{{ route('qr.print', $batch) }}"
                               class="btn btn-sm btn-gold">
                                إعادة الطباعة
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center">
                            لا توجد عمليات سابقة.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $batches->links() }}

</div>

@endsection
