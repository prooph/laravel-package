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
 * Use this helper in a Laravel migrations script to set up the snapshot store schema
 *
 * Call the appropriated methods in your up() / down() method
 */
final class SnapshotSchema
{
    /**
     * Creates a snapshot schema
     *
     * @param Schema $schema
     * @param string $snapshotName Defaults to 'snapshot'
     */
    public static function create($snapshotName = 'snapshot')
    {
        Schema::create($snapshotName, function (Blueprint $snapshot) use ($snapshotName) {
            // UUID4 of linked aggregate
            $snapshot->char('aggregate_id', 36);
            // Class of the linked aggregate
            $snapshot->string('aggregate_type', 150);
            // Version of the aggregate after event was recorded
            $snapshot->integer('last_version', false, true);
            // DateTime ISO8601 + microseconds UTC stored as a string e.g. 2016-02-02T11:45:39.000000
            $snapshot->char('created_at', 26);
            $snapshot->binary('aggregate_root');

            $snapshot->index(['aggregate_id', 'aggregate_type'], $snapshotName . '_m_v_uix');
        });
    }

    /**
     * Drop a snapshot schema
     *
     * @param string $snapshotName Defaults to 'snapshot'
     */
    public static function drop($snapshotName = 'snapshot')
    {
        Schema::drop($snapshotName);
    }
}
