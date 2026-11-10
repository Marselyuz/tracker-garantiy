@extends('template.app')
@section('title', 'Добавить чек')
@section('page')

<section class="form-card card">
    <h2 class="form-title">Шаг 1. Распознать чек</h2>

    <form method="POST" action="{{ route('receipts.recognize') }}" enctype="multipart/form-data" class="custom-form">
        @csrf
        <div class="form-group">
            <input type="file" name="receipt_image" class="form-input file-input" accept="image/png, image/jpeg" required>
        </div>
        <div class="form-actions">
            <button type="submit" class="btn btn-primary btn-block">Загрузить и распознать</button>
        </div>
    </form>
</section>

<section class="form-card card" style="margin-top: 20px;">
    <h2 class="form-title">Шаг 2. Сохранить чек и товары</h2>

    <form method="POST" action="{{ route('receipts.store') }}" enctype="multipart/form-data" class="custom-form">
        @csrf

        <div class="form-group">
            <label class="form-label">Фото чека</label>
            
            @if(session('pending_receipt_image'))
                <div style="margin-bottom: 15px; padding: 10px; background: #222; border-radius: 8px; border: 1px solid #333;">
                    <img src="{{ asset('storage/' . session('pending_receipt_image')) }}" alt="Фото чека" style="max-height: 150px; border-radius: 6px; display: block; margin-bottom: 10px;">
                    <span style="font-size: 12px; color: #888;">Фото уже загружено и будет сохранено вместе с чеком.</span>
                </div>
            @else
                <input type="file" name="image" class="form-input file-input" accept="image/png, image/jpeg">
            @endif
        </div>

        <div class="form-group">
            <label class="form-label">Магазин</label>
            <input type="text" name="store_name" class="form-input" value="{{ old('store_name', session('recognized_store')) }}" required>
        </div>

        <div class="form-group">
            <label class="form-label">Дата покупки</label>
            <input type="date" name="purchase_date" class="form-input" value="{{ old('purchase_date', session('recognized_date')) }}" required>
        </div>

        @php $formItems = old('items', session('recognized_items', [])); @endphp
        @if(!empty($formItems))
            <h3 class="form-title" style="margin-top: 20px; font-size: 16px;">Найденные товары</h3>
            @foreach($formItems as $index => $item)
                <div style="background: #222; padding: 15px; border-radius: 8px; margin-bottom: 10px; border: 1px solid #333;">
                    <div class="form-group">
                        <label class="form-label">Название товара</label>
                        <input type="text" name="items[{{ $index }}][name]" class="form-input" value="{{ $item['name'] }}" required>
                    </div>
                    <div class="form-group" style="display: flex; gap: 10px;">
                        <div style="flex: 1;">
                            <label class="form-label">Цена</label>
                            <input type="number" step="0.01" name="items[{{ $index }}][price]" class="form-input" value="{{ $item['price'] }}" required>
                        </div>
                        <div style="flex: 1;">
                            <label class="form-label">Гарантия (мес.)</label>
                            <input type="number" name="items[{{ $index }}][warranty_months]" class="form-input" value="{{ $item['warranty_months'] ?? 12 }}" required>
                        </div>
                    </div>
                </div>
            @endforeach
        @endif

        <div class="form-actions" style="margin-top: 20px;">
            <button type="submit" class="btn btn-primary btn-block">Сохранить всё</button>
        </div>
    </form>
</section>
@endsection