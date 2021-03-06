<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TicketClosed extends Notification implements ShouldQueue
{
    use Queueable;

    private $title;
    private $url;
    private $reason;
    private $is_user;

    public function __construct($id, $title, $url, $reason, $is_user = null)
    {
        $this->id = $id;
        $this->title = $title;
        $this->url = $url;
        $this->reason = $reason;
        $this->is_user = $is_user;
    }

    public function via($notifiable)
    {
        return $this->is_user ? ['mail'] : sysConfig('ticket_closed_notification');
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject(trans('notification.close_ticket', ['id' => $this->id, 'title' => $this->title]))
            ->line($this->reason)
            ->action(trans('notification.view_ticket'), $this->url)
            ->line(__('If your problem has not been solved, Feel free to open other one.'));
    }

    public function toCustom($notifiable)
    {
        return [
            'title'   => trans('notification.close_ticket', ['id' => $this->id, 'title' => $this->title]),
            'content' => $this->reason,
        ];
    }
}
