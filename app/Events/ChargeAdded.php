<?php

namespace App\Events;

use App\Models\BillItem;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ChargeAdded
{
    use Dispatchable, SerializesModels;

    public function __construct(public BillItem $billItem) {}
}