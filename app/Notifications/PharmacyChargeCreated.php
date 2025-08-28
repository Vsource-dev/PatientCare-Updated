<?php
// app/Notifications/PharmacyChargeCreated.php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\PharmacyCharge;

class PharmacyChargeCreated extends Notification
{
    use Queueable;

    protected PharmacyCharge $charge;

    public function __construct(PharmacyCharge $charge)
    {
        $this->charge = $charge;
    }

    /**
     * Only store in database.
     */
    public function via($notifiable): array
    {
        return ['database'];
    }

    /**
     * Data saved to `notifications.data`.
     */
    public function toDatabase($notifiable): array
    {
        return [
            'type'          => 'Pharmacy',
            'event'         => 'Charge Created',
            'charge_id'     => $this->charge->id,
            'rx_number'     => $this->charge->rx_number,
            'amount'        => $this->charge->total_amount,
            'created_at'    => $this->charge->created_at->toDateTimeString(),
            'message'       => "A new medication charge ({$this->charge->rx_number}) for ₱" .
                                number_format($this->charge->total_amount,2) .
                                " has been added to your bill.",
        ];
    }
}
