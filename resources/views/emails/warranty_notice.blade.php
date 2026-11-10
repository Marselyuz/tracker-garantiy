<div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; background-color: #f9f9f9; border-radius: 10px;">
    <h2 style="color: #6366f1;">Здравствуйте!</h2>
    <p>Напоминаем, что гарантия на ваш товар <b>{{ $item->name }}</b> скоро истекает.</p>
    
    <div style="background-color: #ffffff; padding: 15px; border-radius: 8px; margin: 20px 0; border: 1px solid #e5e7eb;">
        <p style="margin: 5px 0;"> Дата окончания: <b style="color: #ef4444;">{{ \Carbon\Carbon::parse($item->warranty_end_date)->format('d.m.Y') }}</b></p>
        
        @if($item->receipt && $item->receipt->store_name)
            <p style="margin: 5px 0;"> Магазин: <b>{{ $item->receipt->store_name }}</b></p>
        @endif
        
        @if($item->price)
            <p style="margin: 5px 0;"> Цена: <b>{{ number_format($item->price, 0, '', ' ') }} ₽</b></p>
        @endif
    </div>

    <p>Успейте обратиться в сервисный центр, если с товаром возникли проблемы!</p>
    
    <hr style="border: none; border-top: 1px solid #e5e7eb; margin: 30px 0;">
    <small style="color: #9ca3af;">С уважением, система "Мои гарантии"</small>
</div>