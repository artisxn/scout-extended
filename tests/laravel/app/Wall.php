<?php

namespace App;

use codicastudio\ScoutExtended\Searchable\Aggregator;

final class Wall extends Aggregator
{
    protected $models = [
        User::class,
        Thread::class,
        Post::class,
    ];
}
