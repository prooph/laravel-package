<?php
/**
 * prooph (http://getprooph.org/)
 *
 * @see       https://github.com/prooph/laravel-package for the canonical source repository
 * @copyright Copyright (c) 2016 prooph software GmbH (http://prooph-software.com/)
 * @license   https://github.com/prooph/laravel-package/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace ProophTest\Package\Exception;

use Prooph\Common\Messaging\PayloadTrait;
use Prooph\Common\Messaging\Query;
use Prooph\Package\Exception\QueryResultFailed;

final class QueryResultFailedTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_can_check_to_see_if_a_previous_exception_was_passed()
    {
        $query = new class([]) extends Query {
            use PayloadTrait;
        };

        $previousException = new \Exception();
        $queryException = QueryResultFailed::fromQuery($query, [$previousException]);

        static::assertEquals($previousException, $queryException->getPrevious());
    }

    /**
     * Depending on how the implementation is for handling errors a
     * query handler may of done something along the lines of:
     *
     * ```php
     * <?php $deferred->rejected(404, 'Not Found');
     * ```
     *
     * @test
     */
    public function it_will_allow_us_to_get_original_response_data()
    {
        $query = new class([]) extends Query {
            use PayloadTrait;
        };

        $customError = [404, 'Not Found'];
        $queryException = QueryResultFailed::fromQuery($query, $customError);

        static::assertEquals($customError, $queryException->getResponse());
    }

    /**
     * @test
     */
    public function if_no_previous_exception_was_set_it_should_be_null()
    {
        $query = new class([]) extends Query {
            use PayloadTrait;
        };

        $queryException = QueryResultFailed::fromQuery($query, []);

        static::assertNull($queryException->getPrevious());
    }
}
