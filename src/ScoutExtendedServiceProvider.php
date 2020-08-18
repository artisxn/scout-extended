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
use codicastudio\ScoutExtended\Console\Commands\FlushCommand;
use codicastudio\ScoutExtended\Console\Commands\ImportCommand;
use codicastudio\ScoutExtended\Console\Commands\MakeAggregatorCommand;
use codicastudio\ScoutExtended\Console\Commands\OptimizeCommand;
use codicastudio\ScoutExtended\Console\Commands\ReImportCommand;
use codicastudio\ScoutExtended\Console\Commands\StatusCommand;
use codicastudio\ScoutExtended\Console\Commands\SyncCommand;
use codicastudio\ScoutExtended\Engines\codicastudioEngine;
use codicastudio\ScoutExtended\Helpers\SearchableFinder;
use codicastudio\ScoutExtended\Jobs\UpdateJob;
use codicastudio\ScoutExtended\Managers\EngineManager;
use codicastudio\ScoutExtended\Searchable\AggregatorObserver;
use Illuminate\Support\ServiceProvider;
use Laravel\Scout\ScoutServiceProvider;

final class ScoutExtendedServiceProvider extends ServiceProvider
{
    /**
     * {@inheritdoc}
     */
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'codicastudio');
    }

    /**
     * {@inheritdoc}
     */
    public function register(): void
    {
        $this->app->register(ScoutServiceProvider::class);

        $this->registerBinds();
        $this->registerCommands();
        $this->registerMacros();
    }

    /**
     * Binds codicastudio services into the container.
     *
     * @return void
     */
    private function registerBinds(): void
    {
        $this->app->bind(codicastudio::class, function () {
            return new codicastudio($this->app);
        });

        $this->app->alias(codicastudio::class, 'codicastudio');

        $this->app->singleton(EngineManager::class, function ($app) {
            return new EngineManager($app);
        });

        $this->app->alias(EngineManager::class, \Laravel\Scout\EngineManager::class);

        $this->app->bind(codicastudioEngine::class, function (): codicastudioEngine {
            return $this->app->make(\Laravel\Scout\EngineManager::class)->createcodicastudioDriver();
        });

        $this->app->alias(codicastudioEngine::class, 'codicastudio.engine');
        $this->app->bind(SearchClient::class, function (): SearchClient {
            return $this->app->make('codicastudio.engine')->getClient();
        });

        $this->app->alias(SearchClient::class, 'codicastudio.client');

        $this->app->bind(AnalyticsClient::class, function (): AnalyticsClient {
            return AnalyticsClient::create(config('scout.codicastudio.id'), config('scout.codicastudio.secret'));
        });

        $this->app->alias(AnalyticsClient::class, 'codicastudio.analytics');

        $this->app->singleton(AggregatorObserver::class, AggregatorObserver::class);
        $this->app->bind(\Laravel\Scout\Builder::class, Builder::class);

        $this->app->bind(SearchableFinder::class, function () {
            return new SearchableFinder($this->app);
        });
    }

    /**
     * Register artisan commands.
     *
     * @return void
     */
    private function registerCommands(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                MakeAggregatorCommand::class,
                ImportCommand::class,
                FlushCommand::class,
                OptimizeCommand::class,
                ReImportCommand::class,
                StatusCommand::class,
                SyncCommand::class,
            ]);
        }
    }

    /**
     * Register macros.
     *
     * @return void
     */
    private function registerMacros(): void
    {
        \Illuminate\Database\Eloquent\Builder::macro('transform', function (array $array, array $transformers = null) {
            foreach ($transformers ?? UpdateJob::getTransformers() as $transformer) {
                $array = app($transformer)->transform($this->getModel(), $array);
            }

            return $array;
        });
    }
}
