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

namespace codicastudio\ScoutExtended\Settings;

use codicastudio\codicastudioSearch\SearchIndex;
use codicastudio\ScoutExtended\Repositories\LocalSettingsRepository;
use codicastudio\ScoutExtended\Repositories\RemoteSettingsRepository;
use codicastudio\ScoutExtended\Repositories\UserDataRepository;

/**
 * @internal
 */
class Synchronizer
{
    /**
     * @var \codicastudio\ScoutExtended\Settings\Compiler
     */
    private $compiler;

    /**
     * @var \codicastudio\ScoutExtended\Settings\Encrypter
     */
    private $encrypter;

    /**
     * @var \codicastudio\ScoutExtended\Repositories\LocalSettingsRepository
     */
    private $localRepository;

    /**
     * @var \codicastudio\ScoutExtended\Repositories\RemoteSettingsRepository
     */
    private $remoteRepository;

    /**
     * @var \codicastudio\ScoutExtended\Repositories\UserDataRepository
     */
    private $userDataRepository;

    /**
     * Synchronizer constructor.
     *
     * @param \codicastudio\ScoutExtended\Settings\Compiler $compiler
     * @param \codicastudio\ScoutExtended\Settings\Encrypter $encrypter
     * @param \codicastudio\ScoutExtended\Repositories\LocalSettingsRepository $localRepository
     * @param \codicastudio\ScoutExtended\Repositories\RemoteSettingsRepository $remoteRepository
     * @param \codicastudio\ScoutExtended\Repositories\UserDataRepository $userDataRepository
     *
     * @return void
     */
    public function __construct(
        Compiler $compiler,
        Encrypter $encrypter,
        LocalSettingsRepository $localRepository,
        RemoteSettingsRepository $remoteRepository,
        UserDataRepository $userDataRepository
    ) {
        $this->compiler = $compiler;
        $this->encrypter = $encrypter;
        $this->localRepository = $localRepository;
        $this->remoteRepository = $remoteRepository;
        $this->userDataRepository = $userDataRepository;
    }

    /**
     * Analyses the settings of the given index.
     *
     * @param \codicastudio\codicastudioSearch\SearchIndex $index
     *
     * @return \codicastudio\ScoutExtended\Settings\Status
     */
    public function analyse(SearchIndex $index): Status
    {
        $remoteSettings = $this->remoteRepository->find($index);

        return new Status($this->localRepository, $this->userDataRepository, $this->encrypter, $remoteSettings, $index);
    }

    /**
     * Downloads the settings of the given index.
     *
     * @param \codicastudio\codicastudioSearch\SearchIndex $index
     *
     * @return void
     */
    public function download(SearchIndex $index): void
    {
        $settings = $this->remoteRepository->find($index);

        $path = $this->localRepository->getPath($index);

        $this->compiler->compile($settings, $path);

        $settingsHash = $this->encrypter->encrypt($settings);

        $this->userDataRepository->save($index, ['settingsHash' => $settingsHash]);
    }

    /**
     * Uploads the settings of the given index.
     *
     * @param \codicastudio\codicastudioSearch\SearchIndex $index
     *
     * @return void
     */
    public function upload(SearchIndex $index): void
    {
        $settings = $this->localRepository->find($index);

        $settingsHash = $this->encrypter->encrypt($settings);

        $this->userDataRepository->save($index, ['settingsHash' => $settingsHash]);
        $this->remoteRepository->save($index, $settings);
    }
}
