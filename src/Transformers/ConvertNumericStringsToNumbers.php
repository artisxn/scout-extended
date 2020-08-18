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

namespace codicastudio\ScoutExtended\Transformers;

use codicastudio\ScoutExtended\Contracts\TransformerContract;
use function is_string;

final class ConvertNumericStringsToNumbers implements TransformerContract
{
    /**
     * Converts the given array numeric strings to numbers.
     *
     * @param object $searchable
     * @param array $array
     *
     * @return array
     */
    public function transform($searchable, array $array): array
    {
        foreach ($array as $key => $value) {
            /*
             * Casts numeric strings to integers/floats.
             */
            if (is_string($value) && is_numeric($value)) {
                $array[$key] = ctype_digit($value) ? (int) $value : (float) $value;
            }
        }

        return $array;
    }
}
