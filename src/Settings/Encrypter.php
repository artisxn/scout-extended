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

/**
 * @internal
 */
final class Encrypter
{
    /**
     * @param  \codicastudio\ScoutExtended\Settings\Settings $settings
     *
     * @return string
     */
    public function encrypt(Settings $settings): string
    {
        return md5(serialize($settings->compiled()));
    }
}
