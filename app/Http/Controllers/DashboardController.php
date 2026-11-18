<?php

namespace App\Http\Controllers;
 
use App\Models\QrCode;
use App\Models\GenerationBatch;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $totalCodes = QrCode::count();

        $totalBatches = GenerationBatch::count();

        $lastCode = QrCode::orderByDesc('serial_number')
            ->value('code');

        $recentBatches = GenerationBatch::withCount('qrCodes')
            ->latest()
            ->take(5)
            ->get();

        $monthlyCodes = QrCode::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        return view('dashboard', compact(
            'totalCodes',
            'totalBatches',
            'lastCode',
            'recentBatches',
            'monthlyCodes'
        ));
    }
}