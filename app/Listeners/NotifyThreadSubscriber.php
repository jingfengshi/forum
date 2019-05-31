<?php

namespace App\Listeners;

use App\Events\ThreadHasReply;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class NotifyThreadSubscriber
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  ThreadHasReply  $event
     * @return void
     */
    public function handle(ThreadHasReply $event)
    {
        $event->thread->notifySubscribers($event->reply);
    }
}
