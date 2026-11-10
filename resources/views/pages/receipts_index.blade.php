@extends('template.app')
@section('title', 'Мои чеки')
@section('page')

<div class="section-head">
    <h1 class="page-title">Мои чеки</h1>
    <a href="{{ route('receipts.create') }}" class="btn btn-primary">+ Добавить чек</a>
</div>

@if($receipts->isEmpty())
    <div class="empty-state">
        <div class="empty-icon">🧾</div>
        <p>У вас пока нет добавленных чеков. Загрузите первый, чтобы начать отслеживать гарантии.</p>
    </div>
@else
    <div class="card-grid">
        @foreach($receipts as $receipt)
            <a href="{{ route('receipts.show', $receipt->id) }}" class="card receipt-card">
                <div class="receipt-card-icon">🛍️</div>
                <div class="card-body">
                    <h3 class="card-title">{{ $receipt->store_name }}</h3>
                    <p class="card-meta">
                        <span class="meta-date">{{ $receipt->purchase_date->format('d.m.Y') }}</span>
                        <span class="meta-dot">·</span>
                        <span class="meta-items">{{ $receipt->items->count() }} товар(ов)</span>
                    </p>
                </div>
            </a>
        @endforeach
    </div>
@endif

@endsection