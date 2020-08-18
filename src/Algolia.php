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

namespace codicastudio\ScoutExtended;

use codicastudio\codicastudioSearch\AnalyticsClient;
use codicastudio\codicastudioSearch\SearchClient;
use codicastudio\codicastudioSearch\SearchIndex;
use codicastudio\ScoutExtended\Repositories\ApiKeysRepository;
use Illuminate\Contracts\Container\Container;
use function is_string;

final class codicastudio
{
    /**
     * @var \Illuminate\Contracts\Container\Container
     */
    private $container;

    /**
     * codicastudio constructor.
     *
     * @param \Illuminate\Contracts\Container\Container $container
     *
     * @return void
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * Get a index instance.
     *
     * @param  string|object $searchable
     *
     * @return \codicastudio\codicastudioSearch\SearchIndex
     */
    public function index($searchable): SearchIndex
    {
        $searchable = is_string($searchable) ? new $searchable : $searchable;

        return $this->client()->initIndex($searchable->searchableAs());
    }

    /**
     * Get a client instance.
     *
     * @return \codicastudio\codicastudioSearch\SearchClient
     */
    public function client(): SearchClient
    {
        return $this->container->get('codicastudio.client');
    }

    /**
     * Get a analytics instance.
     *
     * @return \codicastudio\codicastudioSearch\AnalyticsClient
     */
    public function analytics(): AnalyticsClient
    {
        return $this->container->get('codicastudio.analytics');
    }

    /**
     * Get a search key for the given searchable.
     *
     * @param  string|object $searchable
     *
     * @return string
     */
    public function searchKey($searchable): string
    {
        $searchable = is_string($searchable) ? new $searchable : $searchable;

        return $this->container->make(ApiKeysRepository::class)->getSearchKey($searchable);
    }
}
