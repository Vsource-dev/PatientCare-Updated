<?php
// app/Notifications/AdmissionCharged.php
namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\DatabaseMessage;

class AdmissionCharged extends Notification
{
    use Queueable;

    protected float $bedRate;
    protected float $roomRate;
    protected float $doctorRate;
    protected int   $billingItemId;

    public function __construct(float $bedRate, float $roomRate, float $doctorRate, int $billingItemId)
    {
        $this->bedRate       = $bedRate;
        $this->roomRate      = $roomRate;
        $this->doctorRate    = $doctorRate;
        $this->billingItemId = $billingItemId;
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): DatabaseMessage
    {
        $total = $this->bedRate + $this->roomRate + $this->doctorRate;

        return new DatabaseMessage([
            'type'            => 'Billing',
            'title'           => "₱" . number_format($total, 2) . " in admission charges",
            'bed_rate'        => $this->bedRate,
            'room_rate'       => $this->roomRate,
            'doctor_rate'     => $this->doctorRate,
            'billing_item_id' => $this->billingItemId,
        ]);
    }
}
