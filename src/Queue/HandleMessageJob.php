<?php
/**
 * prooph (http://getprooph.org/)
 *
 * @see       https://github.com/prooph/laravel-package for the canonical source repository
 * @copyright Copyright (c) 2016 prooph software GmbH (http://prooph-software.com/)
 * @license   https://github.com/prooph/laravel-package/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Prooph\Package\Queue;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Prooph\Common\Messaging\Message;
use Prooph\Package\Facades\CommandBus;
use Prooph\Package\Facades\EventBus;
use Prooph\Package\Facades\QueryBus;

class HandleMessageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

    /** @var Message */
    private $message; // this variable (and the whole object) will serialized before storing in the queue

    /**
     * AsyncMessageHandler constructor.
     *
     * @param Message $message
     */
    public function __construct(Message $message)
    {
        $this->message = $message;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // This code will be executed after pulling this class from the queue and deserialization
        //
        // pass the message to the Message Bus
        //
        switch ($this->message->messageType()) {
            case Message::TYPE_COMMAND:
                CommandBus::dispatch($this->message);
                break;
            case Message::TYPE_QUERY:
                QueryBus::dispatch($this->message);
                break;
            case Message::TYPE_EVENT:
                EventBus::dispatch($this->message);
                break;
        }
    }
}
