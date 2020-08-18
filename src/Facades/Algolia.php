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

namespace codicastudio\ScoutExtended\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \codicastudio\codicastudioSearch\SearchIndex index($searchable)
 * @method static \codicastudio\codicastudioSearch\SearchClient client()
 * @method static \codicastudio\codicastudioSearch\AnalyticsClient analytics()
 * @method static string searchKey($searchable)
 *
 * @see \codicastudio\ScoutExtended\codicastudio
 */
final class codicastudio extends Facade
{
    /**
     * {@inheritdoc}
     */
    protected static function getFacadeAccessor(): string
    {
        return 'codicastudio';
    }
}
