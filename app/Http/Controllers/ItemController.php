<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Receipt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ItemController extends Controller
{
    public function store(Request $request, $receiptId)
    {
        $receipt = Receipt::where('user_id', Auth::id())->findOrFail($receiptId);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'nullable|numeric|min:0',
            'warranty_amount' => 'required|integer|min:1',
            'warranty_type' => 'required|in:weeks,months,years',
        ], [
            'name.required' => 'Введите название товара.',
            'name.max' => 'Название товара слишком длинное.',
            'price.numeric' => 'Цена должна быть числом.',
            'price.min' => 'Цена не может быть отрицательной.',
            'warranty_amount.required' => 'Укажите срок гарантии.',
            'warranty_amount.integer' => 'Срок гарантии должен быть целым числом.',
            'warranty_amount.min' => 'Срок гарантии должен быть больше нуля.',
            'warranty_type.required' => 'Выберите тип гарантии.',
        ]);

        $purchaseDate = Carbon::parse($receipt->purchase_date);
        
        if ($validated['warranty_type'] === 'weeks') {
            $endDate = $purchaseDate->copy()->addWeeks($validated['warranty_amount']);
            $monthsFallback = ceil($validated['warranty_amount'] / 4);
        } elseif ($validated['warranty_type'] === 'years') {
            $endDate = $purchaseDate->copy()->addYears($validated['warranty_amount']);
            $monthsFallback = $validated['warranty_amount'] * 12;
        } else {
            $endDate = $purchaseDate->copy()->addMonths($validated['warranty_amount']);
            $monthsFallback = $validated['warranty_amount'];
        }

        Item::create([
            'receipt_id' => $receipt->id,
            'name' => $validated['name'],
            'price' => $validated['price'],
            'warranty_end_date' => $endDate,
            'warranty_months' => $monthsFallback,
        ]);

        return back()->with('success', 'Товар добавлен');
    }

    public function edit($id)
    {
        $item = Item::whereHas('receipt', function ($q) {
            $q->where('user_id', Auth::id());
        })->findOrFail($id);

        return view('pages.item_edit', compact('item'));
    }

    public function update(Request $request, $id)
    {
        $item = Item::whereHas('receipt', function ($q) {
            $q->where('user_id', Auth::id());
        })->findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'nullable|numeric|min:0',
            'warranty_amount' => 'required|integer|min:1',
            'warranty_type' => 'required|in:weeks,months,years',
        ], [
            'name.required' => 'Введите название товара.',
            'name.max' => 'Название товара слишком длинное.',
            'price.numeric' => 'Цена должна быть числом.',
            'price.min' => 'Цена не может быть отрицательной.',
            'warranty_amount.required' => 'Укажите срок гарантии.',
            'warranty_amount.integer' => 'Срок гарантии должен быть целым числом.',
            'warranty_amount.min' => 'Срок гарантии должен быть больше нуля.',
            'warranty_type.required' => 'Выберите тип гарантии.',
        ]);

        $purchaseDate = Carbon::parse($item->receipt->purchase_date);
        
        if ($validated['warranty_type'] === 'weeks') {
            $endDate = $purchaseDate->copy()->addWeeks($validated['warranty_amount']);
            $monthsFallback = ceil($validated['warranty_amount'] / 4);
        } elseif ($validated['warranty_type'] === 'years') {
            $endDate = $purchaseDate->copy()->addYears($validated['warranty_amount']);
            $monthsFallback = $validated['warranty_amount'] * 12;
        } else {
            $endDate = $purchaseDate->copy()->addMonths($validated['warranty_amount']);
            $monthsFallback = $validated['warranty_amount'];
        }

        $item->update([
            'name' => $validated['name'],
            'price' => $validated['price'],
            'warranty_end_date' => $endDate,
            'warranty_months' => $monthsFallback,
        ]);

        return redirect()->route('receipts.show', $item->receipt_id)->with('success', 'Товар обновлён');
    }

    public function destroy($id)
    {
        $item = Item::whereHas('receipt', function ($q) {
            $q->where('user_id', Auth::id());
        })->findOrFail($id);

        $receiptId = $item->receipt_id;
        $item->delete();

        return redirect()->route('receipts.show', $receiptId)->with('success', 'Товар удалён');
    }
}