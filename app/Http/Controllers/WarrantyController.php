<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WarrantyController extends Controller
{
    public function index(Request $request)
    {
        $items = Item::whereHas('receipt', function ($q) {
            $q->where('user_id', Auth::id());
        })->with('receipt')->get();

        $filter = $request->query('status');

        if ($filter) {
            $items = $items->filter(fn ($i) => $i->warranty_status === $filter);
        }

        // сортировка: сначала те, что скорее истекают
        $items = $items->sortBy('warranty_end_date');

        return view('pages.warranties_index', compact('items', 'filter'));
    }
}
