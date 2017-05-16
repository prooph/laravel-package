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

    use Prooph\Common\Messaging\Command;
    use Prooph\ServiceBus\Plugin\Router\CommandRouter;

    final class PostTwitterStatus extends Command
    {
        private $tweet;

        public static function funnyTweet(string $tweet): PostTwitterStatus
        {
            return new self('LOL - ' . $tweet);
        }

        public static function shockingTweet(string $tweet): PostTwitterStatus
        {
            return new self('OMG - ' . $tweet);
        }

        private function __construct(string $tweet)
        {
            $this->init();
            $this->tweet = $tweet;
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

    CommandBus::utilize(new CommandRouter([
        'PostTwitterStatus' => function (PostTwitterStatus $command): void {
            // You know the drill... curl_exec....
        },
    ]));

    CommandBus::dispatch(PostTwitterStatus::shockingTweet('prooph is awesome.'));
    CommandBus::dispatch(PostTwitterStatus::funnyTweet('cats rule.'));

}
