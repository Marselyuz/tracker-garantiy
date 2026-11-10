@extends('template.app')
@section('title', 'Панель администратора')
@section('page')

<div class="section-head">
    <h1 class="page-title">Панель администратора</h1>
</div>

<div class="card-grid" style="margin-bottom: 40px; display: flex; gap: 20px; flex-wrap: wrap;">
    <div class="card" style="flex: 1; min-width: 200px; text-align: center; padding: 24px;">
        <h3 style="margin: 0; color: var(--text-secondary);">Пользователей</h3>
        <p style="font-size: 2.5rem; font-weight: bold; margin: 10px 0 0; color: var(--text-primary);">{{ $totalUsers }}</p>
    </div>
    <div class="card" style="flex: 1; min-width: 200px; text-align: center; padding: 24px;">
        <h3 style="margin: 0; color: var(--text-secondary);">Загружено чеков</h3>
        <p style="font-size: 2.5rem; font-weight: bold; margin: 10px 0 0; color: var(--accent-color);">{{ $totalReceipts }}</p>
    </div>
    <div class="card" style="flex: 1; min-width: 200px; text-align: center; padding: 24px;">
        <h3 style="margin: 0; color: var(--text-secondary);">Всего товаров</h3>
        <p style="font-size: 2.5rem; font-weight: bold; margin: 10px 0 0; color: var(--success-color);">{{ $totalItems }}</p>
    </div>
</div>

<h2 class="section-subtitle" style="margin-bottom: 20px;">Список пользователей</h2>
<div class="card" style="padding: 0; overflow-x: auto;">
    <table style="width: 100%; border-collapse: collapse; text-align: left;">
        <thead>
            <tr style="border-bottom: 1px solid var(--border-color); background: rgba(255,255,255,0.02);">
                <th style="padding: 16px;">ID</th>
                <th style="padding: 16px;">Имя</th>
                <th style="padding: 16px;">Email</th>
                <th style="padding: 16px;">Чеков</th>
                <th style="padding: 16px;">Роль</th>
                <th style="padding: 16px;">Действия</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $user)
            <tr style="border-bottom: 1px solid var(--border-color);">
                <td style="padding: 16px;">{{ $user->id }}</td>
                <td style="padding: 16px; font-weight: 600;">{{ $user->name ?? $user->username ?? 'Без имени' }}</td>
                <td style="padding: 16px;">{{ $user->email }}</td>
                <td style="padding: 16px;">{{ $user->receipts_count }}</td>
                <td style="padding: 16px;">
                    @if($user->role === 'admin')
                        <span style="color: #60a5fa; font-weight: bold;">Админ</span>
                    @elseif($user->role === 'banned')
                        <span style="color: #ef4444; font-weight: bold;">Забанен</span>
                    @else
                        <span style="color: #9ca3af;">Пользователь</span>
                    @endif
                </td>
                <td style="padding: 16px; display: flex; gap: 10px;">
                    @if(auth()->id() !== $user->id)
                        @if($user->role !== 'banned')
                            <form action="{{ route('admin.users.role', $user->id) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="role" value="banned">
                                <button type="submit" style="background: #ef4444; color: white; border: none; padding: 6px 12px; border-radius: 4px; cursor: pointer; font-size: 12px;">Забанить</button>
                            </form>
                        @else
                            <form action="{{ route('admin.users.role', $user->id) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="role" value="user">
                                <button type="submit" style="background: #10b981; color: white; border: none; padding: 6px 12px; border-radius: 4px; cursor: pointer; font-size: 12px;">Разбанить</button>
                            </form>
                        @endif
                    @else
                        <span style="color: #6b7280; font-size: 12px;">Это вы</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

@endsection