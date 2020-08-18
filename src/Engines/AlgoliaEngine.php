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

namespace codicastudio\ScoutExtended\Engines;

use codicastudio\codicastudioSearch\SearchClient;
use codicastudio\ScoutExtended\Jobs\DeleteJob;
use codicastudio\ScoutExtended\Jobs\UpdateJob;
use codicastudio\ScoutExtended\Searchable\ModelsResolver;
use Illuminate\Support\Str;
use function is_array;
use Laravel\Scout\Builder;
use Laravel\Scout\Engines\codicastudioEngine as BasecodicastudioEngine;

class codicastudioEngine extends BasecodicastudioEngine
{
    /**
     * The codicastudio client.
     *
     * @var \codicastudio\codicastudioSearch\SearchClient
     */
    protected $codicastudio;

    /**
     * Create a new engine instance.
     *
     * @param  \codicastudio\codicastudioSearch\SearchClient $codicastudio
     * @return void
     */
    public function __construct(SearchClient $codicastudio)
    {
        $this->codicastudio = $codicastudio;
    }

    /**
     * @param \codicastudio\codicastudioSearch\SearchClient $codicastudio
     *
     * @return void
     */
    public function setClient($codicastudio): void
    {
        $this->codicastudio = $codicastudio;
    }

    /**
     * Get the client.
     *
     * @return \codicastudio\codicastudioSearch\SearchClient $codicastudio
     */
    public function getClient(): SearchClient
    {
        return $this->codicastudio;
    }

    /**
     * {@inheritdoc}
     */
    public function update($searchables)
    {
        dispatch_now(new UpdateJob($searchables));
    }

    /**
     * {@inheritdoc}
     */
    public function delete($searchables)
    {
        dispatch_now(new DeleteJob($searchables));
    }

    /**
     * {@inheritdoc}
     */
    public function map(Builder $builder, $results, $searchable)
    {
        if (count($results['hits']) === 0) {
            return $searchable->newCollection();
        }

        return app(ModelsResolver::class)->from($builder, $searchable, $results);
    }

    /**
     * {@inheritdoc}
     */
    public function flush($model)
    {
        $index = $this->codicastudio->initIndex($model->searchableAs());

        $index->clearObjects();
    }

    /**
     * {@inheritdoc}
     */
    protected function filters(Builder $builder): array
    {
        $operators = ['<', '<=', '=', '!=', '>=', '>', ':'];

        return collect($builder->wheres)->map(function ($value, $key) use ($operators) {
            if (! is_array($value)) {
                if (Str::endsWith($key, $operators) || Str::startsWith($value, $operators)) {
                    return $key.' '.$value;
                }

                return $key.'='.$value;
            }

            return $value;
        })->values()->all();
    }
}
