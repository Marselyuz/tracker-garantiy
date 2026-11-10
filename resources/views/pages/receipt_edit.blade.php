@extends('template.app')
@section('title', 'Изменить чек')
@section('page')

<section class="form-card card">
    <h2 class="form-title">Изменить чек</h2>

    <form method="POST" action="{{ route('receipts.update', $receipt->id) }}" enctype="multipart/form-data" class="custom-form">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label class="form-label">Магазин</label>
            <input type="text" name="store_name" class="form-input" value="{{ old('store_name', $receipt->store_name) }}">
            @error('store_name')<span class="field-error">{{ $message }}</span>@enderror
        </div>

        <div class="form-group">
            <label class="form-label">Дата покупки</label>
            <input type="date" name="purchase_date" class="form-input" value="{{ old('purchase_date', $receipt->purchase_date->format('Y-m-d')) }}">
            @error('purchase_date')<span class="field-error">{{ $message }}</span>@enderror
        </div>

        <div class="form-group">
            <label class="form-label">Заменить фото чека (необязательно)</label>
            <input type="file" name="image" class="form-input file-input" accept="image/png, image/jpeg">
            @error('image')<span class="field-error">{{ $message }}</span>@enderror
        </div>

        <div class="form-actions">
            <a href="{{ route('receipts.show', $receipt->id) }}" class="btn btn-secondary">Отмена</a>
            <button type="submit" class="btn btn-primary btn-block">Сохранить изменения</button>
        </div>
    </form>
</section>

@endsection