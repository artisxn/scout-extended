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

namespace codicastudio\ScoutExtended\Managers;

use codicastudio\codicastudioSearch\SearchClient;
use codicastudio\codicastudioSearch\Support\UserAgent;
use codicastudio\ScoutExtended\Engines\codicastudioEngine;
use Laravel\Scout\EngineManager as BaseEngineManager;

class EngineManager extends BaseEngineManager
{
    /**
     * Create an codicastudio engine instance.
     *
     * @return \codicastudio\ScoutExtended\Engines\codicastudioEngine
     */
    public function createcodicastudioDriver(): codicastudioEngine
    {
        UserAgent::addCustomUserAgent('Laravel Scout Extended', '1.9.0');

        return new codicastudioEngine(SearchClient::create(config('scout.codicastudio.id'), config('scout.codicastudio.secret')));
    }
}
