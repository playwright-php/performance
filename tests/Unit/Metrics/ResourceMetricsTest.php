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
use Playwright\Performance\Metrics\ResourceMetrics;

#[CoversClass(ResourceMetrics::class)]
class ResourceMetricsTest extends TestCase
{
    public function testToArray(): void
    {
        $metrics = new ResourceMetrics(
            'https://example.com',
            100.0,
            1024,
            'script',
            0.0,
            10.0,
            10.0,
            15.0,
            15.0,
            20.0,
            20.0,
            30.0,
            100.0
        );

        $array = $metrics->toArray();

        $this->assertEquals('https://example.com', $array['url']);
        $this->assertEquals(100.0, $array['duration']);
        $this->assertEquals(1024, $array['size']);
        $this->assertEquals('script', $array['type']);
        $this->assertEquals(5.0, $array['dnsTime']);
        $this->assertEquals(5.0, $array['connectionTime']);
        $this->assertEquals(10.0, $array['ttfb']);
        $this->assertEquals(70.0, $array['downloadTime']);
    }
}
