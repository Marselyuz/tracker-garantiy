@extends('template.app')
@section('title', 'Мои гарантии')
@section('page')

<h1 class="page-title">Мои гарантии</h1>

<div class="filter-bar">
    <a href="{{ route('warranties.index') }}" class="filter-chip {{ !$filter ? 'active' : '' }}">Все</a>
    <a href="{{ route('warranties.index', ['status' => 'active']) }}" class="filter-chip {{ $filter === 'active' ? 'active' : '' }}">✅ Активные</a>
    <a href="{{ route('warranties.index', ['status' => 'expiring']) }}" class="filter-chip {{ $filter === 'expiring' ? 'active' : '' }}">⏳ Истекают</a>
    <a href="{{ route('warranties.index', ['status' => 'expired']) }}" class="filter-chip {{ $filter === 'expired' ? 'active' : '' }}">❌ Истекли</a>
</div>

@if($items->isEmpty())
    <div class="empty-state">
        <div class="empty-icon">🛡️</div>
        <p>Гарантий с таким статусом пока не найдено.</p>
    </div>
@else
    <div class="item-list">
        @foreach($items as $item)
            <a href="{{ route('receipts.show', $item->receipt_id) }}" class="item-row card item-row-link">
                <div class="item-main">
                    <span class="item-name">{{ $item->name }}</span>
                    <span class="item-store">🏪 {{ $item->receipt->store_name }}</span>
                </div>
                <div class="item-meta">
                    <span class="badge badge-{{ $item->warranty_status }}">
                        @if($item->warranty_status === 'active') ✅ Активна
                        @elseif($item->warranty_status === 'expiring') ⏳ Истекает
                        @else ❌ Истекла
                        @endif
                    </span>
                    <span class="item-warranty-date">до {{ $item->warranty_end_date->format('d.m.Y') }}</span>
                </div>
            </a>
        @endforeach
    </div>
@endif

@endsection