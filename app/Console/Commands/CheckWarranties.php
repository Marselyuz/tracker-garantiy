<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Item;
use App\Models\User;
use App\Mail\WarrantyNoticeMail;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class CheckWarranties extends Command
{
    protected $signature = 'warranties:check';
    protected $description = 'Умная проверка гарантий на основе личных настроек пользователей';

    public function handle()
    {
        $activeDays = User::where('notifications_enabled', true)
            ->distinct()
            ->pluck('notify_days')
            ->filter();

        $count = 0;

        foreach ($activeDays as $days) {
            $targetDate = Carbon::today()->addDays($days);

            $items = Item::whereDate('warranty_end_date', $targetDate)
                ->whereHas('receipt.user', function ($q) use ($days) {
                    $q->where('notifications_enabled', true)
                      ->where('notify_days', $days);
                })
                ->with('receipt.user')
                ->get();

            foreach ($items as $item) {
                $user = $item->receipt->user;
                Mail::to($user->email)->send(new WarrantyNoticeMail($item));
                $count++;
            }
        }

        $this->info("Проверка завершена. Отправлено писем: {$count}");
    }
}