<?php

namespace App\Listeners;

use App\Events\OurExampleEvent;
use Illuminate\Support\Facades\Log;
use Spatie\LaravelIgnition\Recorders\LogRecorder\LogMessage;
use Illuminate\Broadcasting\Broadcasters\LogBroadcaster;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class OurExampleListener
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
    public function handle(OurExampleEvent $event)
    {
        Log::debug("The user {$event->username} just performed {$event->action}");
    }
}
