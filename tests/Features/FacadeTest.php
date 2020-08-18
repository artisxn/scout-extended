<?php

declare(strict_types=1);

namespace Tests\Features;

use codicastudio\ScoutExtended\codicastudio;
use codicastudio\ScoutExtended\Facades\codicastudio as codicastudioFacade;
use Tests\TestCase;

final class FacadeTest extends TestCase
{
    public function testFacadeResolvedService(): void
    {
        $this->assertInstanceOf(codicastudio::class, codicastudioFacade::getFacadeRoot());
    }
}
