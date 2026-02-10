<?php

namespace App\Concerns;

trait WithNotifications
{
    public function notifySuccess($message): void
    {
        $this->dispatch('show-notification', [
            'type' => 'success',
            'message' => $message,
        ]);
    }

    public function notifyError($message): void
    {
        $this->dispatch('show-notification', [
            'type' => 'error',
            'message' => $message,
        ]);
    }

    public function notifyWarning($message): void
    {
        $this->dispatch('show-notification', [
            'type' => 'warning',
            'message' => $message,
        ]);
    }

    public function notifyInfo($message): void
    {
        $this->dispatch('show-notification', [
            'type' => 'info',
            'message' => $message,
        ]);
    }
}
