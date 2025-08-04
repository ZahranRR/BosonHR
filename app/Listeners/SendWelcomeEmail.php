<?php

namespace App\Listeners;

use App\Events\UserCreated;
use App\Mail\WelcomeUserMail;
use Illuminate\Support\Facades\Mail;

class SendWelcomeEmail
{
    public function handle(UserCreated $event)
    {
        Mail::to($event->user->email)->send(new WelcomeUserMail($event->user, $event->password));
    }
}
