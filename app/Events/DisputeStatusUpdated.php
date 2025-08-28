<?php

namespace App\Events;

use App\Models\Dispute;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DisputeStatusUpdated
{
    use Dispatchable, SerializesModels;

    public function __construct(public Dispute $dispute) {}
}