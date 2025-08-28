<?php
// app/Notifications/DepositReceived.php

namespace App\Notifications;

use App\Models\Deposit;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class DepositReceived extends Notification
{
    use Queueable;

    protected Deposit $deposit;

    public function __construct(Deposit $deposit)
    {
        $this->deposit = $deposit;
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        return [
            'type'         => 'deposit',
            'deposit_id'   => $this->deposit->deposit_id,
            'amount'       => $this->deposit->amount,
            'deposited_at' => $this->deposit->deposited_at->toDateTimeString(),
            'message'      => "A payment of ₱".number_format($this->deposit->amount,2)." has been posted to your account.",
        ];
    }
}
