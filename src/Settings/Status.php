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
use codicastudio\ScoutExtended\Repositories\UserDataRepository;
use Illuminate\Support\Str;
use LogicException;

/**
 * @internal
 */
final class Status
{
    /**
     * @var \codicastudio\ScoutExtended\Settings\Encrypter
     */
    private $encrypter;

    /**
     * @var \codicastudio\ScoutExtended\Repositories\UserDataRepository
     */
    private $userDataRepository;

    /**
     * @var \codicastudio\ScoutExtended\Repositories\LocalSettingsRepository
     */
    private $localRepository;

    /**
     * @var \codicastudio\ScoutExtended\Settings\Settings
     */
    private $remoteSettings;

    /**
     * @var \codicastudio\codicastudioSearch\SearchIndex
     */
    private $index;

    public const LOCAL_NOT_FOUND = 'localNotFound';

    public const  REMOTE_NOT_FOUND = 'remoteNotFound';

    public const  BOTH_ARE_EQUAL = 'bothAreEqual';

    public const  LOCAL_GOT_UPDATED = 'localGotUpdated';

    public const  REMOTE_GOT_UPDATED = 'remoteGotUpdated';

    public const  BOTH_GOT_UPDATED = 'bothGotUpdated';

    /**
     * Status constructor.
     *
     * @param \codicastudio\ScoutExtended\Repositories\LocalSettingsRepository $localRepository
     * @param \codicastudio\ScoutExtended\Settings\Encrypter $encrypter
     * @param \codicastudio\ScoutExtended\Settings\Settings $remoteSettings
     * @param \codicastudio\codicastudioSearch\SearchIndex $index
     *
     * @return void
     */
    public function __construct(
        LocalSettingsRepository $localRepository,
        UserDataRepository $userDataRepository,
        Encrypter $encrypter,
        Settings $remoteSettings,
        SearchIndex $index
    ) {
        $this->encrypter = $encrypter;
        $this->localRepository = $localRepository;
        $this->userDataRepository = $userDataRepository;
        $this->remoteSettings = $remoteSettings;
        $this->index = $index;
    }

    /**
     * @return bool
     */
    public function localNotFound(): bool
    {
        return ! $this->localRepository->exists($this->index);
    }

    /**
     * @return bool
     */
    public function remoteNotFound(): bool
    {
        return empty($this->userDataRepository->getSettingsHash($this->index));
    }

    /**
     * @return bool
     */
    public function bothAreEqual(): bool
    {
        return $this->encrypter->encrypt($this->localRepository->find($this->index)) ===
            $this->userDataRepository->getSettingsHash($this->index) &&
            $this->encrypter->encrypt($this->remoteSettings) === $this->userDataRepository->getSettingsHash($this->index);
    }

    /**
     * @return bool
     */
    public function localGotUpdated(): bool
    {
        return $this->encrypter->encrypt($this->localRepository->find($this->index)) !==
            $this->userDataRepository->getSettingsHash($this->index) &&
            $this->encrypter->encrypt($this->remoteSettings) === $this->userDataRepository->getSettingsHash($this->index);
    }

    /**
     * @return bool
     */
    public function remoteGotUpdated(): bool
    {
        return $this->encrypter->encrypt($this->localRepository->find($this->index)) ===
            $this->userDataRepository->getSettingsHash($this->index) &&
            $this->encrypter->encrypt($this->remoteSettings) !== $this->userDataRepository->getSettingsHash($this->index);
    }

    /**
     * @return bool
     */
    public function bothGotUpdated(): bool
    {
        return $this->encrypter->encrypt($this->localRepository->find($this->index)) !==
            $this->userDataRepository->getSettingsHash($this->index) &&
            $this->encrypter->encrypt($this->remoteSettings) !== $this->userDataRepository->getSettingsHash($this->index);
    }

    /**
     * Get the current state.
     *
     * @return string
     */
    public function toString(): string
    {
        $methods = [
            self::LOCAL_NOT_FOUND,
            self::REMOTE_NOT_FOUND,
            self::BOTH_ARE_EQUAL,
            self::LOCAL_GOT_UPDATED,
            self::REMOTE_GOT_UPDATED,
            self::BOTH_GOT_UPDATED,
        ];

        foreach ($methods as $method) {
            if ($this->{$method}()) {
                return $method;
            }
        }

        throw new LogicException('This should not happen');
    }

    /**
     * Get a human description of the current status.
     *
     * @return string
     */
    public function toHumanString(): string
    {
        $string = Str::snake($this->toString());

        return Str::ucfirst(str_replace('_', ' ', $string));
    }
}
