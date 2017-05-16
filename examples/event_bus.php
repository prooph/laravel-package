<?php
/**
 * prooph (http://getprooph.org/)
 *
 * @see       https://github.com/prooph/laravel-package for the canonical source repository
 * @copyright Copyright (c) 2016 prooph software GmbH (http://prooph-software.com/)
 * @license   https://github.com/prooph/laravel-package/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace {

    // Requires ProophServiceProvider to be registered in your Laravel application.

    use Prooph\Common\Messaging\DomainEvent;
    use Prooph\ServiceBus\Plugin\Router\EventRouter;

    final class TwitterStatusWasPosted extends DomainEvent
    {
        private $tweet;

        public static function withTweet(string $tweet): TwitterStatusWasPosted
        {
            return new self($tweet);
        }

        private function __construct(string $tweet)
        {
            $this->init();
            $this->tweet = $tweet;
        }

        public function readTweet(): string
        {
            return $this->tweet;
        }

        public function payload(): array
        {
            return ['tweet' => $this->tweet];
        }

        protected function setPayload(array $payload): void
        {
            $this->tweet = $payload['tweet'];
        }
    }

    EventBus::utilize(new EventRouter([
        'TwitterStatusWasPosted' => [
            function (TwitterStatusWasPosted $event): void {
                // Mail the admins the wicked news.
            },
        ],
    ]));

    EventBus::dispatch(TwitterStatusWasPosted::withTweet('prooph is awesome.'));

}
