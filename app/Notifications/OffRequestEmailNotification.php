<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OffRequestEmailNotification extends Notification
{
    use Queueable;

    protected $offrequest;

    public function __construct($offrequest)
    {
        $this->offrequest = $offrequest;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage())
            ->subject('New Leave Application from ' . $this->offrequest->name)
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('There is a new leave request from the following employee:')
            ->line('Name: ' . $this->offrequest->name)
            ->line('Leave Title: ' . $this->offrequest->title)
            ->line('Description: ' . $this->offrequest->description)
            ->line('Start: ' . $this->offrequest->start_event)
            ->line('End: ' . $this->offrequest->end_event)
            // ->action('Lihat Detail', url('Employee/offrequest/' . $this->offrequest->id))
            ->line('Thank you for using our system!')
            ->salutation('Best regards, Grobmedia');
    }

    public function toArray($notifiable)
    {
        return [
                // Isi notifikasi dalam bentuk array
            ];
    }
}
