@extends('template.app')
@section('title', 'Изменить товар')
@section('page')

<section class="form-card card">
    <h2 class="form-title">Изменить товар</h2>

    <form method="POST" action="{{ route('items.update', $item->id) }}" class="custom-form">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label class="form-label">Название товара</label>
            <input type="text" name="name" class="form-input" value="{{ old('name', $item->name) }}" placeholder="Введите название товара...">
            @error('name')<span class="field-error">{{ $message }}</span>@enderror
        </div>

        <div class="form-group">
            <label class="form-label">Цена, ₽</label>
            <input type="number" step="0.01" name="price" class="form-input" value="{{ old('price', $item->price) }}" placeholder="0.00">
            @error('price')<span class="field-error">{{ $message }}</span>@enderror
        </div>

        <div class="form-group">
            <label class="form-label">Срок гарантии</label>
            <div style="display: flex; gap: 5px;">
                <input type="number" name="warranty_amount" class="form-input" value="{{ old('warranty_amount', $item->warranty_months) }}" placeholder="Например: 2" style="width: 50%;" required>
                <select name="warranty_type" class="form-input" style="width: 50%; background-color: #222; color: #fff;" required>
                    <option value="weeks" style="background-color: #222; color: #fff;">Нед.</option>
                    <option value="months" selected style="background-color: #222; color: #fff;">Мес.</option>
                    <option value="years" style="background-color: #222; color: #fff;">Лет</option>
                </select>
            </div>
            @error('warranty_amount')<span class="field-error">{{ $message }}</span>@enderror
            @error('warranty_type')<span class="field-error">{{ $message }}</span>@enderror
        </div>

        <div class="form-actions">
            <a href="{{ url()->previous() }}" class="btn btn-secondary">Отмена</a>
            <button type="submit" class="btn btn-primary btn-block">Сохранить</button>
        </div>
    </form>
</section>

@endsection