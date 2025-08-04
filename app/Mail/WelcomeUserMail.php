<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WelcomeUserMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $password;

    public function __construct($user, $password)
    {
        $this->user = $user;
        $this->password = $password;
    }

    public function build()
    {
        return $this->subject('Welcome to Our Application')
                    ->view('emails.welcome_user')
                    ->with([
                        'userName' => $this->user->name,
                        'email' => $this->user->email,
                        'password' => $this->password,
                    ]);
    }
}
