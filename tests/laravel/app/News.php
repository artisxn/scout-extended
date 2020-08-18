<?php

namespace App;

use codicastudio\ScoutExtended\Searchable\Aggregator;

final class News extends Aggregator
{
    protected $models = [
        User::class,
        Thread::class,
        Post::class,
    ];

    protected $relations = [
        User::class => ['threads'],
    ];
}
