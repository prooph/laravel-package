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

    use Prooph\Common\Messaging\Query;
    use Prooph\Package\Exception\QueryResultFailed;
    use Prooph\ServiceBus\Plugin\Router\QueryRouter;
    use React\Promise\Deferred;

    final class GetTweets extends Query
    {
        /** @var string */
        private $orderBy = 'tweets.created_at ASC';

        public static function latest(): GetTweets
        {
            $query = new self();
            $query->orderBy = 'tweets.created_at DESC';

            return $query;
        }

        private function __construct()
        {
            $this->init();
        }

        public function payload(): array
        {
            return ['order_by' => $this->orderBy];
        }

        protected function setPayload(array $payload): void
        {
            $this->orderBy = $payload['order_by'];
        }
    }

    QueryBus::utilize(new QueryRouter([
        'GetTweets' => function (GetTweets $command, Deferred $deferred): void {
            // You know the drill... mysql query or something remote....
            $deferred->resolve([
                'data' => [
                    [
                        'tweet' => 'I like prooph',
                        'created_at' => '2017-04-27',
                    ],
                ]
            ]);
        },
    ]));

    // The following two code blocks represent a way of getting
    // and handling the result from a query.

    QueryBus::dispatch(GetTweets::latest())
        ->done(function ($result) {
            // Handle tweets here.
        }, function ($error) {
            // Handle error here.
        });


    try {
        $result = QueryBus::resultFrom(GetTweets::latest());
    } catch (QueryResultFailed $error) {
        //
    }

}
