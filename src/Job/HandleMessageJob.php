<?php
/**
 * prooph (http://getprooph.org/)
 *
 * @see       https://github.com/prooph/laravel-package for the canonical source repository
 * @copyright Copyright (c) 2016 prooph software GmbH (http://prooph-software.com/)
 * @license   https://github.com/prooph/laravel-package/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Prooph\Package\Job;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Prooph\Common\Messaging\DomainMessage;
use Prooph\Common\Messaging\Message;
use Prooph\Common\Messaging\MessageDataAssertion;

class HandleMessageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;
    
    /** @var Message|string */
    private $message;
    
    /**
     * AsyncMessageHandler constructor.
     *
     * @param Message $message
     */
    public function __construct(Message $message)
    {
        // Queries cannot be handled in async way, are they?
        
        $this->message = $message;
    }
    
    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // pass the message to the Message Bus
        switch ($this->message->messageType()) {
            case Message::TYPE_COMMAND:
                \CommandBus::dispatch($this->message);
                break;
            case Message::TYPE_QUERY:
                \QueryBus::dispatch($this->message);
                break;
            case Message::TYPE_EVENT:
                \EventBus::dispatch($this->message);
                break;
        }
    }
    
    
    /**
     * Serialize Prooph message in order to persist it in the queue with no data corruption
     *
     *
     * @param Message $message
     *
     * @return string
     */
    public function serializeMessage(DomainMessage $message): string
    {
        $message_data = $message->toArray();
        MessageDataAssertion::assert($message_data);
        
        // replace DateTimeImmutable object with string representation
        $message_data['created_at'] = $message->createdAt()->format('Y-m-d\TH:i:s.u');
        
        return json_encode($message_data);
    }
    
    
    /**
     * Unserialize string and convert it to original Prooph message
     *
     *
     * @param string $serialized_message
     *
     * @return DomainMessage
     */
    private function unserializeMessage(string $serialized_message): DomainMessage
    {
        $message_data = json_decode($serialized_message, true);
        
        $message_data['created_at'] = \DateTimeImmutable::createFromFormat(
            'Y-m-d\TH:i:s.u',
            $message_data['created_at'],
            new \DateTimeZone('UTC')
        );
        
        return DomainMessage::fromArray($message_data);
    }
    
    /**
     * __sleep called when object goes to the queue
     * Ref: Illuminate/Queue/Queue@createObjectPayload()
     *
     *
     * @return void
     */
    function __sleep()
    {
        $this->message = $this->serializeMessage($this->message);
    }
    
    /**
     * __wakeup called when job being pulled from the queue
     *
     * @return void
     */
    function __wakeup()
    {
        $this->message = $this->unserializeMessage($this->message);
    }
    
    
}
