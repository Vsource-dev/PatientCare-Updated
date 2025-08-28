<?php

namespace App\Notifications;

use App\Models\ServiceAssignment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ORChargeCreated extends Notification
{
    use Queueable;

    protected ServiceAssignment $assignment;

    public function __construct(ServiceAssignment $assignment)
    {
        $this->assignment = $assignment;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'type'           => 'or_charge',
            'assignment_id'  => $this->assignment->assignment_id,
            'service_name'   => $this->assignment->service->service_name,
            'amount'         => $this->assignment->amount,
            'assigned_at'    => $this->assignment->datetime->toDateTimeString(),
            'message'        => "An OR charge for “{$this->assignment->service->service_name}” (₱{$this->assignment->amount}) was added.",
        ];
    }

    // toArray can just mirror toDatabase
    public function toArray($notifiable)
    {
        return $this->toDatabase($notifiable);
    }
}
