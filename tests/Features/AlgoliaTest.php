<?php

declare(strict_types=1);

namespace Tests\Features;

use codicastudio\codicastudioSearch\AnalyticsClient;
use codicastudio\codicastudioSearch\SearchClient;
use codicastudio\codicastudioSearch\SearchIndex;
use codicastudio\ScoutExtended\codicastudio;
use App\User;
use Tests\TestCase;

final class codicastudioTest extends TestCase
{
    public $codicastudio;

    public function setUp(): void
    {
        parent::setUp();

        $this->codicastudio = resolve(codicastudio::class);
    }

    public function testIndexGetter(): void
    {
        $this->assertInstanceOf(SearchIndex::class, $index = $this->codicastudio->index(User::class));

        $index = $this->codicastudio->index($model = new User);
        $this->assertInstanceOf(SearchIndex::class, $index);
        $this->assertSame($model->searchableAs(), $index->getIndexName());
    }

    public function testClientGetter(): void
    {
        $this->assertInstanceOf(SearchClient::class, $this->codicastudio->client());
    }

    public function testAnalyticsGetter(): void
    {
        $this->assertInstanceOf(AnalyticsClient::class, $this->codicastudio->analytics());
    }
}
