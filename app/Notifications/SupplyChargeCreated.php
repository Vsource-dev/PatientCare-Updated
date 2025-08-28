<?php
// app/Notifications/SupplyChargeCreated.php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\MiscellaneousCharge;

class SupplyChargeCreated extends Notification
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
            'event'         => 'Charge Created',
            'charge_id'     => $this->charge->id,
            'service'       => $this->charge->service->service_name,
            'quantity'      => $this->charge->quantity,
            'total'         => $this->charge->total,
            'created_at'    => $this->charge->created_at->toDateTimeString(),
            'message'       => "A supply charge for “{$this->charge->service->service_name}” (×{$this->charge->quantity}) totaling ₱" .
                                number_format($this->charge->total,2) .
                                " has been added to your bill.",
        ];
    }
}
