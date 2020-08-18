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
use codicastudio\ScoutExtended\Exceptions\ModelNotFoundException;
use codicastudio\ScoutExtended\Helpers\SearchableFinder;
use codicastudio\ScoutExtended\Repositories\LocalSettingsRepository;
use codicastudio\ScoutExtended\Settings\Compiler;
use codicastudio\ScoutExtended\Settings\LocalFactory;
use Illuminate\Console\Command;

final class OptimizeCommand extends Command
{
    /**
     * {@inheritdoc}
     */
    protected $signature = 'scout:optimize {searchable? : The name of the searchable}';

    /**
     * {@inheritdoc}
     */
    protected $description = 'Optimize the given searchable creating a settings file';

    /**
     * {@inheritdoc}
     */
    public function handle(
        codicastudio $codicastudio,
        LocalFactory $localFactory,
        Compiler $compiler,
        SearchableFinder $searchableFinder,
        LocalSettingsRepository $localRepository
    ) {
        foreach ($searchableFinder->fromCommand($this) as $searchable) {
            $this->output->text('🔎 Optimizing search experience in: <info>['.$searchable.']</info>');
            $index = $codicastudio->index($searchable);
            if (! $localRepository->exists($index) ||
                $this->confirm('Local settings already exists, do you wish to overwrite?')) {
                try {
                    $settings = $localFactory->create($index, $searchable);
                } catch (ModelNotFoundException $e) {
                    $model = $e->getModel();
                    $this->output->error("Model not found [$model] resolving [$searchable] settings. Please seed your database with records of this model.");

                    return 1;
                }
                $path = $localRepository->getPath($index);
                $compiler->compile($settings, $path);
                $this->output->success('Settings file created at: '.$path);
                $this->output->note('Please review the settings file and synchronize it with codicastudio using '.
                    'the Artisan command `scout:sync`.');
            }
        }
    }
}
