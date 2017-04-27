<?php

declare(strict_types=1);
/**
 * prooph (http://getprooph.org/)
 *
 * @see       https://github.com/prooph/laravel-package for the canonical source repository
 * @copyright Copyright (c) 2016 prooph software GmbH (http://prooph-software.com/)
 * @license   https://github.com/prooph/laravel-package/blob/master/LICENSE.md New BSD License
 */

namespace Prooph\Package\Migration\Schema;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Use this helper in a Laravel migrations script to set up the event store schema
 *
 * Call the appropriated methods in your up() / down() method
 */
final class EventStoreSchema
{
    /**
     * Use this method when you work with a single stream strategy
     *
     * @param string $streamName Defaults to 'event_stream'
     * @param bool $withCausationColumns Enable causation columns when using prooph/event-store-bus-bridge
     */
    public static function createSingleStream($streamName = 'event_stream', $withCausationColumns = false)
    {
        Schema::create($streamName, function (Blueprint $eventStream) use ($streamName, $withCausationColumns) {
            // UUID4 of the event
            $eventStream->char('event_id', 36);
            // Version of the aggregate after event was recorded
            $eventStream->integer('version', false, true);
            // Name of the event
            $eventStream->string('event_name', 100);
            // Event payload
            $eventStream->text('payload');
            // DateTime ISO8601 + microseconds UTC stored as a string e.g. 2016-02-02T11:45:39.000000
            $eventStream->char('created_at', 26);
            // UUID4 of linked aggregate
            $eventStream->char('aggregate_id', 36);
            // Class of the linked aggregate
            $eventStream->string('aggregate_type', 150);

            if ($withCausationColumns) {
                // UUID4 of the command which caused the event
                $eventStream->char('causation_id', 36);
                // Name of the command which caused the event
                $eventStream->string('causation_name', 100);
            }
            $eventStream->primary('event_id');
            // Concurrency check on database level
            $eventStream->unique(['aggregate_id', 'aggregate_type', 'version'], $streamName . '_m_v_uix');
        });
    }

    /**
     * Use this method when you work with an aggregate type stream strategy
     *
     * @param string $streamName [shortclassname]_stream
     * @param bool $withCausationColumns Enable causation columns when using prooph/event-store-bus-bridge
     */
    public static function createAggregateTypeStream($streamName, $withCausationColumns = false)
    {
        Schema::create($streamName, function (Blueprint $eventStream) use ($streamName, $withCausationColumns) {
            // UUID4 of the event
            $eventStream->char('event_id', 36);
            // Version of the aggregate after event was recorded
            $eventStream->integer('version', false, true);
            // Name of the event
            $eventStream->string('event_name', 100);
            // Event payload
            $eventStream->text('payload');
            // DateTime ISO8601 + microseconds UTC stored as a string e.g. 2016-02-02T11:45:39.000000
            $eventStream->char('created_at', 26);
            // UUID4 of linked aggregate
            $eventStream->char('aggregate_id', 36);
            // Class of the linked aggregate
            $eventStream->string('aggregate_type', 150);

            if ($withCausationColumns) {
                // UUID4 of the command which caused the event
                $eventStream->char('causation_id', 36);
                // Name of the command which caused the event
                $eventStream->string('causation_name', 100);
            }
            $eventStream->primary('event_id');
            // Concurrency check on database level
            $eventStream->unique(['aggregate_id', 'version'], $streamName . '_m_v_uix');
        });
    }

    /**
     * Drop a stream schema
     *
     * @param string $streamName Defaults to 'event_stream'
     */
    public static function dropStream($streamName = 'event_stream')
    {
        Schema::drop($streamName);
    }
}
