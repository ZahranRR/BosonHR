<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OvertimeStatusMail extends Mailable
{
    use Queueable, SerializesModels;

    public $status;
    public $overtime;

    public function __construct($overtime, $status)
    {
        $this->overtime = $overtime;
        $this->status = $status;
    }

    public function build()
    {
        return $this->subject('Status of your Overtime Application')
                    ->view('Employee.Overtime.overtime-status');
    }
}
