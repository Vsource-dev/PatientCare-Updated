<?php

namespace App\Notifications;

use App\Models\Dispute;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;        // ← add this
use Illuminate\Notifications\Notification;

class DisputeFiled extends Notification implements ShouldQueue
{
    use Queueable;                                   // ← add this if you like

    public function __construct(public Dispute $dispute) {}

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'message'    => 'A new billing dispute was filed.',
            'dispute_id' => $this->dispute->dispute_id,
        ];
    }
}
