@extends('template.app')
@section('title', 'Дашборд')
@section('page')

<h1 class="page-title">Дашборд</h1>

<div class="stat-grid">
    <div class="stat-card">
        <span class="stat-num">{{ $stats['receipts_count'] }}</span>
        <span class="stat-label">🧾 Чеков загружено</span>
    </div>
    <div class="stat-card stat-active">
        <span class="stat-num">{{ $stats['active'] }}</span>
        <span class="stat-label">✅ Гарантий активно</span>
    </div>
    <div class="stat-card stat-expiring">
        <span class="stat-num">{{ $stats['expiring'] }}</span>
        <span class="stat-label">⏳ Истекают (≤14 дней)</span>
    </div>
    <div class="stat-card stat-expired">
        <span class="stat-num">{{ $stats['expired'] }}</span>
        <span class="stat-label">❌ Истекли</span>
    </div>
</div>

<div class="section-head">
    <h2>Последние чеки</h2>
    <a href="{{ route('receipts.create') }}" class="btn btn-primary">+ Добавить чек</a>
</div>

@if($recentReceipts->isEmpty())
    <div class="empty-state">
        <div class="empty-icon">📂</div>
        <p>Чеков пока нет. Загрузите первый, чтобы начать отслеживать гарантии.</p>
    </div>
@else
    <div class="receipt-list">
        @foreach($recentReceipts as $receipt)
            <a href="{{ route('receipts.show', $receipt->id) }}" class="receipt-row">
                <div class="receipt-info">
                    <span class="receipt-store">{{ $receipt->store_name }}</span>
                    <span class="receipt-date">{{ $receipt->purchase_date->format('d.m.Y') }}</span>
                </div>
                <span class="receipt-badge">{{ $receipt->items->count() }} тов.</span>
            </a>
        @endforeach
    </div>
@endif

@endsection