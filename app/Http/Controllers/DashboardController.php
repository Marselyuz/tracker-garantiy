<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $items = \App\Models\Item::whereHas('receipt', function ($q) {
            $q->where('user_id', Auth::id());
        })->get();

        $stats = [
            'receipts_count' => Auth::user()->receipts()->count(),
            'active' => $items->filter(fn ($i) => $i->warranty_status === 'active')->count(),
            'expiring' => $items->filter(fn ($i) => $i->warranty_status === 'expiring')->count(),
            'expired' => $items->filter(fn ($i) => $i->warranty_status === 'expired')->count(),
        ];

        $recentReceipts = Auth::user()->receipts()->latest()->take(5)->get();

        return view('pages.dashboard', compact('stats', 'recentReceipts'));
    }
}
