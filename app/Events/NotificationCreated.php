<?php

namespace App\Events;

use App\Models\Notification;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NotificationCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $notification;

    public function __construct(Notification $notification)
    {
        $this->notification = $notification;
        \Log::info('📡 NotificationCreated Event fired', ['id' => $notification->id]);
    }

    public function broadcastOn(): array
    {
        // اگر notification برای یک کاربر خاص است (notifiable)
        if ($this->notification->notifiable_type && $this->notification->notifiable_id) {
            $userType = class_basename($this->notification->notifiable_type);
            $userId = $this->notification->notifiable_id;

            return [new PrivateChannel("{$userType}.{$userId}")];
        }

        if ($this->notification->notifiable_type) {
            $userType = class_basename($this->notification->notifiable_type);
            return [new PrivateChannel('Admins')];
        }

        // اگر برای همه است (system notification)
        return [
            new Channel('notifications.public'),
        ];
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->notification->id,
            'title' => $this->notification->title,
            'message' => $this->notification->message,
            'type' => $this->notification->type,
            'priority' => $this->notification->priority,
            'meta' => $this->notification->meta,
            'created_at' => $this->notification->created_at->toDateTimeString(),
        ];
    }

    public function broadcastAs()
    {
        return 'NotificationCreated';
    }
}