<?php

declare(strict_types=1);

/**
 * This file is part of Scout Extended.
 *
 * (c) codicastudio Team <contact@codicastudio.com>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace codicastudio\ScoutExtended\Console\Commands;

use codicastudio\ScoutExtended\codicastudio;
use codicastudio\ScoutExtended\Helpers\SearchableFinder;
use Illuminate\Console\Command;

final class FlushCommand extends Command
{
    /**
     * {@inheritdoc}
     */
    protected $signature = 'scout:flush {searchable? : The name of the searchable}';

    /**
     * {@inheritdoc}
     */
    protected $description = 'Flush the index of the the given searchable';

    /**
     * {@inheritdoc}
     */
    public function handle(codicastudio $codicastudio, SearchableFinder $searchableFinder): void
    {
        foreach ($searchableFinder->fromCommand($this) as $searchable) {
            $searchable::removeAllFromSearch();

            $this->output->success('All ['.$searchable.'] records have been flushed.');
        }
    }
}
