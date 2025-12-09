<?php

namespace App\Filament\Student\Widgets;

use App\Models\StudentNotification;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;

class NotificationsWidget extends Widget
{
    protected static string $view = 'filament.student.widgets.notifications-widget';
    
    protected int | string | array $columnSpan = 'full';
    
    protected static ?string $pollingInterval = '30s';
    
    public function getNotifications()
    {
        $user = Auth::user();
        
        if (!$user || !$user->murid) {
            \Log::warning('NotificationsWidget: User or murid not found', [
                'user_id' => $user?->id,
                'has_murid' => $user ? ($user->murid ? 'yes' : 'no') : 'no_user',
            ]);
            return collect();
        }
        
        $notifications = StudentNotification::where('murid_id', $user->murid->id)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
        
        \Log::info('NotificationsWidget: Loaded notifications', [
            'murid_id' => $user->murid->id,
            'count' => $notifications->count(),
        ]);
        
        return $notifications;
    }
    
    public function getUnreadCount(): int
    {
        $user = Auth::user();
        
        if (!$user || !$user->murid) {
            return 0;
        }
        
        return StudentNotification::where('murid_id', $user->murid->id)
            ->unread()
            ->count();
    }
    
    public function markAsRead($notificationId): void
    {
        $user = Auth::user();
        
        if (!$user || !$user->murid) {
            return;
        }
        
        $notification = StudentNotification::where('id', $notificationId)
            ->where('murid_id', $user->murid->id)
            ->first();
        
        if ($notification) {
            $notification->markAsRead();
        }
    }
    
    public function markAllAsRead(): void
    {
        $user = Auth::user();
        
        if (!$user || !$user->murid) {
            return;
        }
        
        StudentNotification::where('murid_id', $user->murid->id)
            ->unread()
            ->update(['read_at' => now()]);
    }
}
