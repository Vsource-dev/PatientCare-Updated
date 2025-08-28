<?php
// app/Notifications/PharmacyChargeDispensed.php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\PharmacyCharge;

class PharmacyChargeDispensed extends Notification
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
            'event'         => 'Dispensed',
            'charge_id'     => $this->charge->id,
            'rx_number'     => $this->charge->rx_number,
            'amount'        => $this->charge->total_amount,
            'dispensed_at'  => $this->charge->dispensed_at?->toDateTimeString(),
            'message'       => "Your medication order ({$this->charge->rx_number}) for ₱" .
                                number_format($this->charge->total_amount,2) .
                                " has been dispensed.",
        ];
    }
}
