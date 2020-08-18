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

final class ConvertDatesToTimestamps implements TransformerContract
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
            $attributeValue = $searchable->getModel()->getAttribute($key);

            /*
             * Casts carbon instances to timestamp.
             */
            if ($attributeValue instanceof \Illuminate\Support\Carbon) {
                $array[$key] = $attributeValue->getTimestamp();
            }
        }

        return $array;
    }
}
