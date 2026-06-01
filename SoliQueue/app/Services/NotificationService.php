<?php

namespace App\Services;

use App\Models\Notification;
use Illuminate\Database\Eloquent\Collection;
use Carbon\Carbon;

class NotificationService extends BaseService
{
    public function __construct(Notification $model)
    {
        $this->model = $model;
    }

    public function createNotification(int $candidatId, string $titre, string $message): Notification
    {
        return $this->create([
            'candidat_id' => $candidatId,
            'titre' => $titre,
            'message' => $message,
            'dateEnvoi' => Carbon::now(),
            'estLu' => false,
        ]);
    }

    public function getUnreadNotifications(int $candidatId): Collection
    {
        return $this->model
            ->where('candidat_id', $candidatId)
            ->where('estLu', false)
            ->orderBy('dateEnvoi', 'desc')
            ->get();
    }

    public function markAsRead(int $notificationId): bool
    {
        $notification = $this->findOrFail($notificationId);
        return $notification->update(['estLu' => true]);
    }
}
