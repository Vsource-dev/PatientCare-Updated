<?php
// app/Notifications/LabChargeCreated.php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\ServiceAssignment;

class LabChargeCreated extends Notification
{
    use Queueable;

    protected ServiceAssignment $assignment;

    public function __construct(ServiceAssignment $assignment)
    {
        $this->assignment = $assignment;
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        return [
            'type'            => 'Laboratory',
            'event'           => 'Order Placed',
            'assignment_id'   => $this->assignment->assignment_id,
            'service'         => $this->assignment->service->service_name,
            'doctor'          => optional($this->assignment->doctor)->doctor_name,
            'ordered_at'      => $this->assignment->created_at->toDateTimeString(),
            'message'         => "Your lab order for “{$this->assignment->service->service_name}” has been placed.",
        ];
    }
}
