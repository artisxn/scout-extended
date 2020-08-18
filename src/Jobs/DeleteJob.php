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

namespace codicastudio\ScoutExtended\Jobs;

use codicastudio\codicastudioSearch\SearchClient;
use codicastudio\ScoutExtended\Searchable\ObjectIdEncrypter;
use Illuminate\Support\Collection;

/**
 * @internal
 */
final class DeleteJob
{
    /**
     * @var \Illuminate\Support\Collection
     */
    private $searchables;

    /**
     * DeleteJob constructor.
     *
     * @param \Illuminate\Support\Collection $searchables
     *
     * @return void
     */
    public function __construct(Collection $searchables)
    {
        $this->searchables = $searchables;
    }

    /**
     * @param \codicastudio\codicastudioSearch\SearchClient $client
     *
     * @return void
     */
    public function handle(SearchClient $client): void
    {
        if ($this->searchables->isEmpty()) {
            return;
        }

        $index = $client->initIndex($this->searchables->first()->searchableAs());

        $result = $index->deleteBy([
            'tagFilters' => [
                $this->searchables->map(function ($searchable) {
                    return ObjectIdEncrypter::encrypt($searchable);
                })->toArray(),
            ],
        ]);

        if (config('scout.synchronous', false)) {
            $result->wait();
        }
    }
}
