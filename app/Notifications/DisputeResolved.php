<?php

namespace App\Notifications;

use App\Models\Dispute;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;        // ← add this
use Illuminate\Notifications\Notification;

class DisputeResolved extends Notification implements ShouldQueue
{
    use Queueable;                                   // ← and this

    public function __construct(public Dispute $dispute) {}

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'message'   => 'Your billing dispute has been '.$this->dispute->status.'.',
            'status'    => $this->dispute->status,
            'bill_item' => $this->dispute->billItem->billing_item_id,
        ];
    }
}
