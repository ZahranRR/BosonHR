<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OffRequestStatusMail extends Mailable
{
    use Queueable, SerializesModels;

    public $status;
    public $offRequest;

    public function __construct($offRequest, $status)
    {
        $this->offRequest = $offRequest;
        $this->status = $status;
    }

    public function build()
    {
        return $this->subject('Status of your Leave Application')
                    ->view('Employee.Offrequest.offrequest-status');
    }
}
