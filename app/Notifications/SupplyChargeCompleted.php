<?php
// app/Notifications/SupplyChargeCompleted.php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\MiscellaneousCharge;

class SupplyChargeCompleted extends Notification
{
    use Queueable;

    protected MiscellaneousCharge $charge;

    public function __construct(MiscellaneousCharge $charge)
    {
        $this->charge = $charge;
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        return [
            'type'          => 'Supply',
            'event'         => 'Charge Completed',
            'charge_id'     => $this->charge->id,
            'service'       => $this->charge->service->service_name,
            'quantity'      => $this->charge->quantity,
            'total'         => $this->charge->total,
            'completed_at'  => $this->charge->updated_at->toDateTimeString(),
            'message'       => "Your supply request for “{$this->charge->service->service_name}” has been completed.",
        ];
    }
}
