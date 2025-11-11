<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OvertimeRequestNotification extends Notification
{
    use Queueable;

    protected $overtime;

    public function __construct($overtime)
    {
        $this->overtime = $overtime;
    }

    public function via($notifiable)
    {
        return ['mail']; // Kirim via email
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('New Overtime Request from ' . $this->overtime->employee->first_name)
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('You have received a new overtime request from the following employee:')
            ->line('Name: ' . $this->overtime->employee->first_name . ' ' . $this->overtime->employee->last_name)
            ->line('Date: ' . $this->overtime->overtime_date)
            ->line('Duration: ' . $this->overtime->duration . ' hour(s)')
            ->line('Notes: ' . $this->overtime->notes)
            // ->action('Review Request', url('/superadmin/overtime/approvals'))
            ->line('Thank you for using our HR system!')
            ->salutation('Best regards, HR Department');
    }

    public function toArray($notifiable)
    {
        return [
            'employee' => $this->overtime->employee->first_name . ' ' . $this->overtime->employee->last_name,
            'date' => $this->overtime->overtime_date,
            'duration' => $this->overtime->duration,
        ];
    }
}
