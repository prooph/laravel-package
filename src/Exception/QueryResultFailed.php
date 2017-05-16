<?php
/**
 * prooph (http://getprooph.org/)
 *
 * @see       https://github.com/prooph/laravel-package for the canonical source repository
 * @copyright Copyright (c) 2016 prooph software GmbH (http://prooph-software.com/)
 * @license   https://github.com/prooph/laravel-package/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Prooph\Package\Exception;

use Prooph\Common\Messaging\Query;

final class QueryResultFailed extends \RuntimeException
{
    /** @var Query */
    private $failedQuery;

    /** @var array */
    private $response;

    /**
     * @param Query|mixed $failedQuery
     * @param array $response
     * @return QueryResultFailed
     */
    public static function fromQuery($failedQuery, array $response): QueryResultFailed
    {
        $previousException = null;

        if (count($response) > 0 && $response[0] instanceof \Throwable) {
            $previousException = $response[0];
        }

        $error = new self(sprintf(
            'Query %s failed to get a response.',
            self::getQueryName($failedQuery),
            $previousException
        ), 0, $previousException);

        $error->failedQuery = $failedQuery;
        $error->response = $response;

        return $error;
    }

    private static function getQueryName($query): string
    {
        if (is_object($query)) {
            return get_class($query);
        }

        if (is_array($query)) {
            return json_encode($query);
        }

        return (string) $query;
    }

    public function getFailedQuery()
    {
        return $this->failedQuery;
    }

    public function getResponse(): array
    {
        return $this->response;
    }
}
