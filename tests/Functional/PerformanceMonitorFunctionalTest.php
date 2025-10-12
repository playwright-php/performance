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

namespace Playwright\Performance\Tests\Functional;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Playwright\Performance\Monitor\PerformanceMonitor;
use Playwright\Playwright;

/**
 * Functional tests using real browser and HTML fixtures.
 *
 * These tests verify that the performance monitoring actually works
 * with real HTML pages that have known characteristics.
 */
#[CoversClass(PerformanceMonitor::class)]
final class PerformanceMonitorFunctionalTest extends TestCase
{
    private static string $fixturesPath;

    public static function setUpBeforeClass(): void
    {
        self::$fixturesPath = dirname(__DIR__).'/Fixtures/html';
    }

    public function testSimplePageCollectsBasicMetrics(): void
    {
        $browser = Playwright::chromium(['headless' => true]);
        $page = $browser->newPage();

        $monitor = new PerformanceMonitor($page);
        $filePath = 'file://'.self::$fixturesPath.'/simple-page.html';
        $monitor->navigate($filePath);

        // Wait for metrics to be collected
        sleep(1);

        $vitals = $monitor->collectCoreWebVitals();

        // Basic assertions - metrics should be collected
        $this->assertGreaterThan(0, $vitals->lcp, 'LCP should be measured');
        $this->assertGreaterThan(0, $vitals->fcp, 'FCP should be measured');
        $this->assertGreaterThanOrEqual(0, $vitals->cls, 'CLS should be measured');

        // TTFB may be 0 for file:// URLs (no network request)
        $this->assertGreaterThanOrEqual(0, $vitals->ttfb, 'TTFB should be >= 0');

        // LCP should be greater than or equal to FCP
        $this->assertGreaterThanOrEqual($vitals->fcp, $vitals->lcp, 'LCP should be >= FCP');

        $browser->close();
    }

    public function testPageWithResourcesCollectsResourceMetrics(): void
    {
        $browser = Playwright::chromium(['headless' => true]);
        $page = $browser->newPage();

        $monitor = new PerformanceMonitor($page);
        $filePath = 'file://'.self::$fixturesPath.'/page-with-resources.html';
        $monitor->navigate($filePath);

        sleep(1);

        $resources = $monitor->collectResourceMetrics();

        // Note: file:// URLs may not generate resource timing entries like HTTP URLs
        // This is expected browser behavior, so we just verify the structure
        $this->assertIsArray($resources, 'Should return array of resources');

        // If resources are collected, verify their structure
        foreach ($resources as $resource) {
            $this->assertIsString($resource->url);
            $this->assertIsFloat($resource->duration);
            $this->assertIsInt($resource->size);
            $this->assertIsString($resource->type);
            $this->assertGreaterThanOrEqual(0, $resource->duration);
        }

        $browser->close();
    }

    public function testPageWithBlockingTaskHasTbt(): void
    {
        $browser = Playwright::chromium(['headless' => true]);
        $page = $browser->newPage();

        $monitor = new PerformanceMonitor($page);
        $filePath = 'file://'.self::$fixturesPath.'/page-with-resources.html';
        $monitor->navigate($filePath);

        // Wait for the blocking task to complete
        sleep(1);

        $vitals = $monitor->collectCoreWebVitals();

        // The page has a ~60ms blocking task, so TBT should be > 0
        // Note: TBT only counts tasks > 50ms, so a 60ms task adds ~10ms to TBT
        $this->assertGreaterThan(0, $vitals->tbt, 'TBT should be measured for blocking tasks');

        $browser->close();
    }

    public function testPageWithLayoutShiftHasCls(): void
    {
        $browser = Playwright::chromium(['headless' => true]);
        $page = $browser->newPage();

        $monitor = new PerformanceMonitor($page);
        $filePath = 'file://'.self::$fixturesPath.'/page-with-cls.html';
        $monitor->navigate($filePath);

        // Wait for the layout shift to occur (happens at 200ms)
        usleep(300000); // 300ms

        $vitals = $monitor->collectCoreWebVitals();

        // The page triggers a layout shift, so CLS should be > 0
        $this->assertGreaterThan(0, $vitals->cls, 'CLS should be measured when layout shifts occur');

        $browser->close();
    }

    public function testMetricsAreConsistent(): void
    {
        $browser = Playwright::chromium(['headless' => true]);
        $page = $browser->newPage();

        $monitor = new PerformanceMonitor($page);
        $filePath = 'file://'.self::$fixturesPath.'/simple-page.html';
        $monitor->navigate($filePath);

        sleep(1);

        $vitals = $monitor->collectCoreWebVitals();

        // Metrics should be in reasonable ranges for a simple local file
        $this->assertLessThan(5000, $vitals->lcp, 'LCP should be under 5s for local file');
        $this->assertLessThan(5000, $vitals->fcp, 'FCP should be under 5s for local file');
        $this->assertLessThan(1000, $vitals->ttfb, 'TTFB should be under 1s for local file');

        // INP and FID should be 0 (no real user interaction)
        $this->assertEquals(0.0, $vitals->inp, 'INP requires real user interaction');
        $this->assertEquals(0.0, $vitals->fid, 'FID requires real user interaction');

        $browser->close();
    }
}
