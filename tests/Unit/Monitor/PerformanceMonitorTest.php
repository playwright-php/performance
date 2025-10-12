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
use Playwright\Page\PageInterface;
use Playwright\Performance\Metrics\CoreWebVitals;
use Playwright\Performance\Metrics\ResourceMetrics;
use Playwright\Performance\Monitor\PerformanceMonitor;

#[CoversClass(PerformanceMonitor::class)]
final class PerformanceMonitorTest extends TestCase
{
    public function testNavigateUsesLoadWait(): void
    {
        $page = $this->createMock(PageInterface::class);

        $page->expects($this->once())
            ->method('goto')
            ->with('https://example.com', ['waitUntil' => 'load']);

        $monitor = new PerformanceMonitor($page);
        $monitor->navigate('https://example.com');
    }

    public function testCollectCoreWebVitalsMapsEvaluateResult(): void
    {
        $evaluateResult = [
            'lcp' => 123.4,
            'fcp' => 45.6,
            'cls' => 0.17,
            'inp' => 150.0,
            'fid' => 50.0,
            'ttfb' => 100.0,
            'tbt' => 200.0,
        ];

        $page = $this->createMock(PageInterface::class);

        $page->expects($this->once())
            ->method('evaluate')
            ->with($this->stringContains('largest-contentful-paint'))
            ->willReturn($evaluateResult);

        $monitor = new PerformanceMonitor($page);

        $vitals = $monitor->collectCoreWebVitals();

        $this->assertInstanceOf(CoreWebVitals::class, $vitals);
        $this->assertSame(123.4, $vitals->lcp);
        $this->assertSame(45.6, $vitals->fcp);
        $this->assertSame(0.17, $vitals->cls);
        $this->assertSame(150.0, $vitals->inp);
        $this->assertSame(50.0, $vitals->fid);
        $this->assertSame(100.0, $vitals->ttfb);
        $this->assertSame(200.0, $vitals->tbt);
    }

    public function testCollectResourceMetricsCreatesResourceMetricObjects(): void
    {
        $resources = [
            [
                'name' => 'https://cdn.example.com/app.js',
                'duration' => 42.5,
                'transferSize' => 4096,
                'initiatorType' => 'script',
                'startTime' => 10.0,
                'fetchStart' => 10.0,
                'domainLookupStart' => 10.0,
                'domainLookupEnd' => 15.0,
                'connectStart' => 15.0,
                'connectEnd' => 20.0,
                'requestStart' => 20.0,
                'responseStart' => 30.0,
                'responseEnd' => 52.5,
            ],
            [
                'name' => 'https://cdn.example.com/styles.css',
                'duration' => 12.3,
                'transferSize' => 2048,
                'initiatorType' => 'link',
                'startTime' => 5.0,
                'fetchStart' => 5.0,
                'domainLookupStart' => 0.0,
                'domainLookupEnd' => 0.0,
                'connectStart' => 0.0,
                'connectEnd' => 0.0,
                'requestStart' => 5.0,
                'responseStart' => 10.0,
                'responseEnd' => 17.3,
            ],
        ];

        $page = $this->createMock(PageInterface::class);

        $page->expects($this->once())
            ->method('evaluate')
            ->with($this->stringContains('performance.getEntriesByType("resource")'))
            ->willReturn($resources);

        $monitor = new PerformanceMonitor($page);

        $metrics = $monitor->collectResourceMetrics();

        $this->assertCount(2, $metrics);
        $this->assertContainsOnlyInstancesOf(ResourceMetrics::class, $metrics);

        $first = $metrics[0];
        $this->assertSame('https://cdn.example.com/app.js', $first->url);
        $this->assertSame(42.5, $first->duration);
        $this->assertSame(4096, $first->size);
        $this->assertSame('script', $first->type);
        $this->assertSame(5.0, $first->getDnsTime());
        $this->assertSame(5.0, $first->getConnectionTime());
        $this->assertSame(10.0, $first->getTtfb());
        $this->assertSame(22.5, $first->getDownloadTime());

        $second = $metrics[1];
        $this->assertSame('https://cdn.example.com/styles.css', $second->url);
        $this->assertSame(12.3, $second->duration);
        $this->assertSame(2048, $second->size);
        $this->assertSame('link', $second->type);
        $this->assertSame(0.0, $second->getDnsTime());
        $this->assertSame(0.0, $second->getConnectionTime());
        $this->assertSame(5.0, $second->getTtfb());
        $this->assertEqualsWithDelta(7.3, $second->getDownloadTime(), 0.01);
    }
}
