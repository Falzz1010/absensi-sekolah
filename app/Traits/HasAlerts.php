<?php

namespace App\Traits;

use Filament\Notifications\Notification;

trait HasAlerts
{
    /**
     * Show success alert
     */
    protected function alertSuccess(string $title, string $message = ''): void
    {
        Notification::make()
            ->success()
            ->title($title)
            ->body($message)
            ->duration(5000)
            ->send();

        $this->dispatch('alert', [
            'type' => 'success',
            'title' => $title,
            'message' => $message,
            'duration' => 5000
        ]);
    }

    /**
     * Show error alert
     */
    protected function alertError(string $title, string $message = ''): void
    {
        Notification::make()
            ->danger()
            ->title($title)
            ->body($message)
            ->duration(7000)
            ->send();

        $this->dispatch('alert', [
            'type' => 'error',
            'title' => $title,
            'message' => $message,
            'duration' => 7000
        ]);
    }

    /**
     * Show warning alert
     */
    protected function alertWarning(string $title, string $message = ''): void
    {
        Notification::make()
            ->warning()
            ->title($title)
            ->body($message)
            ->duration(6000)
            ->send();

        $this->dispatch('alert', [
            'type' => 'warning',
            'title' => $title,
            'message' => $message,
            'duration' => 6000
        ]);
    }

    /**
     * Show info alert
     */
    protected function alertInfo(string $title, string $message = ''): void
    {
        Notification::make()
            ->info()
            ->title($title)
            ->body($message)
            ->duration(5000)
            ->send();

        $this->dispatch('alert', [
            'type' => 'info',
            'title' => $title,
            'message' => $message,
            'duration' => 5000
        ]);
    }
}
