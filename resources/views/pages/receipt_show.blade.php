@extends('template.app')
@section('title', $receipt->store_name)
@section('page')

<div class="section-head">
    <h1 class="page-title">{{ $receipt->store_name }}</h1>
    <div class="action-group">
        @if($receipt->image)
            <a href="{{ route('receipts.download', $receipt->id) }}" class="btn btn-outline">Скачать чек</a>
        @endif
        <a href="{{ route('receipts.edit', $receipt->id) }}" class="btn btn-outline">Изменить</a>
        <form action="{{ route('receipts.destroy', $receipt->id) }}" method="POST" class="inline-form" onsubmit="return confirm('Удалить чек вместе со всеми товарами?');">
            @csrf @method('DELETE')
            <button type="submit" class="btn btn-danger">Удалить</button>
        </form>
    </div>
</div>

<div class="detail-card card">
    @if($receipt->image)
        <div class="receipt-image-wrapper">
            <img src="{{ asset('storage/' . $receipt->image) }}" alt="Фото чека" class="receipt-image">
        </div>
    @endif
    <div class="detail-body">
        <div class="info-row">
            <span class="info-label">Дата покупки:</span>
            <span class="info-value">{{ $receipt->purchase_date->format('d.m.Y') }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Способ добавления:</span>
            <span class="info-value ocr-status">Распознано и проверено</span>
        </div>
    </div>
</div>

<h2 class="section-subtitle">Товары и гарантии</h2>

@if($receipt->items->isEmpty())
    <div class="empty-state">
        <p>Товары ещё не добавлены. Заполните форму ниже.</p>
    </div>
@else
    <div class="item-list">
        @foreach($receipt->items as $item)
            <div class="item-row card">
                <div class="item-main">
                    <span class="item-name">{{ $item->name }}</span>
                    @if($item->price)
                        <span class="item-price">{{ number_format($item->price, 0, '', ' ') }} ₽</span>
                    @endif
                </div>
                <div class="item-meta">
                    <span class="badge badge-{{ $item->warranty_status }}">
                        @if($item->warranty_status === 'active') Гарантия активна
                        @elseif($item->warranty_status === 'expiring') Истекает скоро
                        @else Гарантия истекла
                        @endif
                    </span>
                    <span class="item-warranty-date">до {{ $item->warranty_end_date->format('d.m.Y') }}</span>
                </div>
                <div class="item-actions">
                    <a href="{{ route('items.edit', $item->id) }}" class="action-link edit-link" title="Изменить"></a>
                    <form action="{{ route('items.destroy', $item->id) }}" method="POST" class="inline-form" onsubmit="return confirm('Удалить товар?');">
                        @csrf @method('DELETE')
                        <button type="submit" class="action-link delete-link" title="Удалить">✕</button>
                    </form>
                </div>
            </div>
        @endforeach
    </div>
@endif

<div class="form-card card form-card-inline">
    <h3 class="form-title">Добавить товар вручную</h3>
    <form method="POST" action="{{ route('items.store', $receipt->id) }}" class="custom-form">
        @csrf
        <div class="form-row">
            <div class="form-group flex-2">
                <label class="form-label">Название товара</label>
                <input type="text" name="name" class="form-input" placeholder="Например: Наушники Sony">
            </div>
            <div class="form-group flex-1">
                <label class="form-label">Цена, ₽</label>
                <input type="number" step="0.01" name="price" class="form-input" placeholder="1500">
            </div>
            <div class="form-group flex-1" style="min-width: 200px;">
                <label class="form-label">Срок гарантии</label>
                <div style="display: flex; gap: 5px;">
                    <input type="number" name="warranty_amount" class="form-input" placeholder="Например: 2" style="width: 50%;">
                    <select name="warranty_type" class="form-input" style="width: 50%; background-color: #222; color: #fff;" required>
                        <option value="weeks" style="background-color: #222; color: #fff;">Нед.</option>
                        <option value="months" selected style="background-color: #222; color: #fff;">Мес.</option>
                        <option value="years" style="background-color: #222; color: #fff;">Лет</option>
                    </select>
                </div>
            </div>
        </div>
        
        <div class="form-errors">
            @error('name')<span class="field-error">{{ $message }}</span>@enderror
            @error('warranty_amount')<span class="field-error">{{ $message }}</span>@enderror
        </div>
        
        <div class="form-actions mt-2">
            <button type="submit" class="btn btn-primary">Добавить товар</button>
        </div>
    </form>
</div>

@endsection