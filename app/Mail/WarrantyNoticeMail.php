<?php

namespace App\Mail;

use App\Models\Item;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WarrantyNoticeMail extends Mailable
{
    use Queueable, SerializesModels;

    public $item;

    public function __construct(Item $item)
    {
        $this->item = $item;
    }

    public function build()
    {
        return $this->subject('Истекает гарантия: ' . $this->item->name)
                    ->view('emails.warranty_notice');
    }
}