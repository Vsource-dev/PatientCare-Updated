<?php
// app/Helpers/Audit.php
namespace App\Helpers;

use App\Models\AuditLog;

class Audit
{
    public static function log(int $billItemId, string $action, string $message, string $actor, string $icon = 'fa-info-circle'): void
    {
        AuditLog::create([
            'bill_item_id' => $billItemId,
            'action'       => $action,
            'message'      => $message,
            'actor'        => $actor,
            'icon'         => $icon,
        ]);
    }
}
