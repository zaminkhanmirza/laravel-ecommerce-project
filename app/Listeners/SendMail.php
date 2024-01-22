<?php

namespace App\Listeners;

use App\Events\CategoryCreated;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class SendMail
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(CategoryCreated $event): void
    {
        $user = User::find($event->userId)->toArray();

        Mail::send('eventMail', $user, function ($message) use ($user){
            $message->to($user['email']);
            $message->subject('Event Testing');
        });
    }
}
