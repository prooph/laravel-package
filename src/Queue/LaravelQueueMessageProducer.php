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

use Prooph\Common\Messaging\Message;
use Prooph\ServiceBus\Async\MessageProducer;
use Prooph\ServiceBus\Exception\RuntimeException;
use React\Promise\Deferred;

final class LaravelQueueMessageProducer implements MessageProducer
{
    
    public function __invoke(Message $message, Deferred $deferred = null): void
    {
        
        // As far as I know - Laravel won't let me to get a promise, so no deferred queries are allowed
        if ($deferred) {
            throw new RuntimeException(__CLASS__ . ' cannot handle query messages which require future responses.');
        }
        
        // Now dispatch the Laravel job to deal with the queue
        dispatch(new HandleMessageJob($message));
    }
    
}