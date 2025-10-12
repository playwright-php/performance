<?php

declare(strict_types=1);

/*
 * This file is part of the community-maintained Playwright PHP project.
 * It is not affiliated with or endorsed by Microsoft.
 *
 * (c) 2025-Present - Playwright PHP - https://github.com/playwright-php
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Playwright\Performance\Tests\Unit\Metrics;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Playwright\Performance\Metrics\CoreWebVitals;

#[CoversClass(CoreWebVitals::class)]
class CoreWebVitalsTest extends TestCase
{
    public function testToArray(): void
    {
        $vitals = new CoreWebVitals(1.0, 2.0, 0.1, 150.0, 50.0, 100.0, 200.0);
        $this->assertEquals([
            'lcp' => 1.0,
            'fcp' => 2.0,
            'cls' => 0.1,
            'inp' => 150.0,
            'fid' => 50.0,
            'ttfb' => 100.0,
            'tbt' => 200.0,
        ], $vitals->toArray());
    }

    public function testGetters(): void
    {
        $vitals = new CoreWebVitals(1.0, 2.0, 0.1, 150.0, 50.0, 100.0, 200.0);
        $this->assertEquals(1.0, $vitals->getLCP());
        $this->assertEquals(2.0, $vitals->getFCP());
        $this->assertEquals(0.1, $vitals->getCLS());
        $this->assertEquals(150.0, $vitals->getINP());
        $this->assertEquals(50.0, $vitals->getFID());
        $this->assertEquals(100.0, $vitals->getTTFB());
        $this->assertEquals(200.0, $vitals->getTBT());
    }
}
