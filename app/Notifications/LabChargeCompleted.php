<?php
// app/Notifications/LabChargeCompleted.php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\ServiceAssignment;

class LabChargeCompleted extends Notification
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
            'event'           => 'Result Ready',
            'assignment_id'   => $this->assignment->assignment_id,
            'service'         => $this->assignment->service->service_name,
            'completed_at'    => $this->assignment->updated_at->toDateTimeString(),
            'message'         => "Your lab “{$this->assignment->service->service_name}” is now completed.",
        ];
    }
}
