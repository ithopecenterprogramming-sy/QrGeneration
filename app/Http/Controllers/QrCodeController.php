<?php

namespace App\Http\Controllers;

use App\Models\QrCode;
use App\Models\GenerationBatch;
use App\Services\QrCodeGenerator;
use Illuminate\Http\Request;

class QrCodeController extends Controller
{
    public function index(Request $request)
    {
        $query = QrCode::with('batch')->latest('id');

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where('code', 'like', "%{$search}%");
        }

        $codes = $query->paginate(25)->withQueryString();

        return view('qr.index', compact('codes'));
    }

    public function create()
    {
        return view('qr.create');
    }

    public function store(
        Request $request,
        QrCodeGenerator $generator
    ) {
        $validated = $request->validate([
            'quantity' => 'required|integer|min:1|max:1000',
        ]);

        $batch = $generator->generate(
            (int) $validated['quantity']
        );

        return redirect()
            ->route('qr.print', $batch)
            ->with('success', 'تم توليد الرموز بنجاح.');
    }

    public function batches()
    {
        $batches = GenerationBatch::withCount('qrCodes')
            ->latest()
            ->paginate(20);

        return view('qr.batches', compact('batches'));
    }

    public function print(GenerationBatch $batch)
    {
        $batch->load('qrCodes');

        return view('qr.print', compact('batch'));
    }

    public function show(QrCode $qrCode)
    {
        $qrCode->load('batch');

        return view('qr.show', compact('qrCode'));
    }
}
