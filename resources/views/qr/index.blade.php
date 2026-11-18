
@extends('layouts.app')

@section('title', 'سجل الرموز')

@section('content')
<style>
    /* تنسيق أزرار Pagination */
    .pagination {
        justify-content: center;
        gap: 5px;
        margin-top: 20px;
        margin-bottom: 0;
    }

    .pagination .page-link {
        font-size: 13px;
        padding: 5px 10px;
        min-width: 32px;
        text-align: center;
        border-radius: 6px !important;
        color: #8b7042;
        border: 1px solid #e8dfd1;
        box-shadow: none;
    }

    .pagination .page-item.active .page-link {
        background-color: #C5A059;
        border-color: #C5A059;
        color: #fff;
    }

    .pagination .page-item.disabled .page-link {
        color: #aaa;
        background-color: #f8f8f8;
    }

    .pagination .page-link:hover {
        background-color: #F9F6F0;
        color: #8b7042;
        border-color: #C5A059;
    }
</style>
<h3 class="fw-bold mb-4">سجل رموز QR</h3>

<div class="content-card">

    <form method="GET" class="row g-2 mb-4">
        <div class="col-md-6">
            <input
                type="text"
                name="search"
                value="{{ request('search') }}"
                class="form-control"
                placeholder="ابحث عن الرقم HMC-000001..."
            >
        </div>

        <div class="col-auto">
            <button class="btn btn-gold">بحث</button>
            <a href="{{ route('qr.index') }}"
               class="btn btn-outline-secondary">
                إعادة تعيين
            </a>
        </div>
    </form>

    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th>صورة QR</th>
                    <th>الرقم التسلسلي</th>
                    <th>عملية التوليد</th>
                    <th>تاريخ التوليد</th>
                    <th>التفاصيل</th>
                </tr>
            </thead>

            <tbody>
                @forelse($codes as $code)
                    <tr>
                        <td>
                            @if($code->image_path)
                                <img
                                    src="{{ asset('storage/'.$code->image_path) }}"
                                    width="55"
                                    alt="QR"
                                >
                            @endif
                        </td>

                        <td class="fw-bold">{{ $code->code }}</td>

                        <td>{{ $code->batch->batch_number }}</td>

                        <td>{{ $code->created_at->format('Y-m-d') }}</td>

                        <td>
                            <a href="{{ route('qr.show', $code) }}"
                               class="btn btn-sm btn-outline-secondary">
                                عرض
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center">
                            لا توجد رموز محفوظة.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $codes->links() }}

</div>

@endsection
