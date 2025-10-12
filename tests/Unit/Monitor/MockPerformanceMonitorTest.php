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

namespace Playwright\Performance\Tests\Unit\Monitor;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Playwright\Performance\Metrics\CoreWebVitals;
use Playwright\Performance\Metrics\ResourceMetrics;
use Playwright\Performance\Monitor\MockPerformanceMonitor;

#[CoversClass(MockPerformanceMonitor::class)]
final class MockPerformanceMonitorTest extends TestCase
{
    public function testNavigateTracksUrls(): void
    {
        $mock = new MockPerformanceMonitor();

        $mock->navigate('https://example.com');
        $mock->navigate('https://google.com');

        $this->assertSame(['https://example.com', 'https://google.com'], $mock->getNavigatedUrls());
    }

    public function testCollectCoreWebVitalsReturnsDefaultMetrics(): void
    {
        $mock = new MockPerformanceMonitor();

        $vitals = $mock->collectCoreWebVitals();

        $this->assertInstanceOf(CoreWebVitals::class, $vitals);
        $this->assertSame(1000.0, $vitals->lcp);
        $this->assertSame(500.0, $vitals->fcp);
        $this->assertSame(0.05, $vitals->cls);
    }

    public function testCollectCoreWebVitalsReturnsSetMetrics(): void
    {
        $mock = new MockPerformanceMonitor();
        $vitals = new CoreWebVitals(100.0, 50.0, 0.01, 80.0, 20.0, 30.0, 150.0);

        $mock->setCoreWebVitals($vitals);

        $this->assertSame($vitals, $mock->collectCoreWebVitals());
    }

    public function testCollectResourceMetricsReturnsEmptyByDefault(): void
    {
        $mock = new MockPerformanceMonitor();

        $this->assertSame([], $mock->collectResourceMetrics());
    }

    public function testCollectResourceMetricsReturnsSetResources(): void
    {
        $mock = new MockPerformanceMonitor();
        $resources = [
            new ResourceMetrics('https://example.com/script.js', 100.0, 1024, 'script', 0.0, 0.0, 0.0, 0.0, 0.0, 0.0, 0.0, 0.0, 100.0),
            new ResourceMetrics('https://example.com/style.css', 50.0, 2048, 'link', 0.0, 0.0, 0.0, 0.0, 0.0, 0.0, 0.0, 0.0, 50.0),
        ];

        $mock->setResourceMetrics($resources);

        $this->assertSame($resources, $mock->collectResourceMetrics());
    }
}
