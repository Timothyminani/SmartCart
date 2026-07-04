<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminNotification;

class AdminNotificationController extends Controller
{
    public function markAsRead(AdminNotification $notification)
    {
        $notification->update([
            'is_read' => true
        ]);

        return redirect($notification->url);
    }
}