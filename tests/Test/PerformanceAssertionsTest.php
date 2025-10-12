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

namespace Playwright\Performance\Tests\Test;

use PHPUnit\Framework\AssertionFailedError;
use PHPUnit\Framework\Attributes\CoversTrait;
use PHPUnit\Framework\TestCase;
use Playwright\Performance\Metrics\CoreWebVitals;
use Playwright\Performance\Metrics\ResourceMetrics;
use Playwright\Performance\Test\PerformanceAssertions;

/**
 * Tests for the PerformanceAssertions trait.
 *
 * This verifies that the public API for performance assertions works correctly.
 */
#[CoversTrait(PerformanceAssertions::class)]
final class PerformanceAssertionsTest extends TestCase
{
    use PerformanceAssertions;

    public function testAssertLcpBelowThresholdPasses(): void
    {
        $vitals = new CoreWebVitals(1000.0, 500.0, 0.05, 0.0, 0.0, 100.0, 50.0);
        $this->assertLcpBelowThreshold($vitals, 1500.0);
        $this->assertTrue(true); // Assert passed
    }

    public function testAssertLcpBelowThresholdFails(): void
    {
        $vitals = new CoreWebVitals(3000.0, 500.0, 0.05, 0.0, 0.0, 100.0, 50.0);

        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessage('LCP 3000.00ms exceeds threshold of 1500.00ms');
        $this->assertLcpBelowThreshold($vitals, 1500.0);
    }

    public function testAssertLcpIsGoodPasses(): void
    {
        $vitals = new CoreWebVitals(2000.0, 500.0, 0.05, 0.0, 0.0, 100.0, 50.0);
        $this->assertLcpIsGood($vitals);
        $this->assertTrue(true);
    }

    public function testAssertLcpIsGoodFails(): void
    {
        $vitals = new CoreWebVitals(3000.0, 500.0, 0.05, 0.0, 0.0, 100.0, 50.0);

        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessage('LCP should be below 2.5s (Good threshold)');
        $this->assertLcpIsGood($vitals);
    }

    public function testAssertFcpBelowThresholdPasses(): void
    {
        $vitals = new CoreWebVitals(1000.0, 500.0, 0.05, 0.0, 0.0, 100.0, 50.0);
        $this->assertFcpBelowThreshold($vitals, 1000.0);
        $this->assertTrue(true);
    }

    public function testAssertFcpBelowThresholdFails(): void
    {
        $vitals = new CoreWebVitals(1000.0, 2000.0, 0.05, 0.0, 0.0, 100.0, 50.0);

        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessage('FCP 2000.00ms exceeds threshold of 1000.00ms');
        $this->assertFcpBelowThreshold($vitals, 1000.0);
    }

    public function testAssertFcpIsGoodPasses(): void
    {
        $vitals = new CoreWebVitals(1000.0, 1500.0, 0.05, 0.0, 0.0, 100.0, 50.0);
        $this->assertFcpIsGood($vitals);
        $this->assertTrue(true);
    }

    public function testAssertFcpIsGoodFails(): void
    {
        $vitals = new CoreWebVitals(1000.0, 2000.0, 0.05, 0.0, 0.0, 100.0, 50.0);

        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessage('FCP should be below 1.8s (Good threshold)');
        $this->assertFcpIsGood($vitals);
    }

    public function testAssertClsBelowThresholdPasses(): void
    {
        $vitals = new CoreWebVitals(1000.0, 500.0, 0.05, 0.0, 0.0, 100.0, 50.0);
        $this->assertClsBelowThreshold($vitals, 0.1);
        $this->assertTrue(true);
    }

    public function testAssertClsBelowThresholdFails(): void
    {
        $vitals = new CoreWebVitals(1000.0, 500.0, 0.5, 0.0, 0.0, 100.0, 50.0);

        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessage('CLS 0.500 exceeds threshold of 0.100');
        $this->assertClsBelowThreshold($vitals, 0.1);
    }

    public function testAssertClsIsGoodPasses(): void
    {
        $vitals = new CoreWebVitals(1000.0, 500.0, 0.05, 0.0, 0.0, 100.0, 50.0);
        $this->assertClsIsGood($vitals);
        $this->assertTrue(true);
    }

    public function testAssertClsIsGoodFails(): void
    {
        $vitals = new CoreWebVitals(1000.0, 500.0, 0.5, 0.0, 0.0, 100.0, 50.0);

        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessage('CLS should be below 0.1 (Good threshold)');
        $this->assertClsIsGood($vitals);
    }

    public function testAssertInpBelowThresholdPasses(): void
    {
        $vitals = new CoreWebVitals(1000.0, 500.0, 0.05, 100.0, 0.0, 100.0, 50.0);
        $this->assertInpBelowThreshold($vitals, 150.0);
        $this->assertTrue(true);
    }

    public function testAssertInpBelowThresholdFails(): void
    {
        $vitals = new CoreWebVitals(1000.0, 500.0, 0.05, 300.0, 0.0, 100.0, 50.0);

        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessage('INP 300.00ms exceeds threshold of 150.00ms');
        $this->assertInpBelowThreshold($vitals, 150.0);
    }

    public function testAssertInpIsGoodPasses(): void
    {
        $vitals = new CoreWebVitals(1000.0, 500.0, 0.05, 100.0, 0.0, 100.0, 50.0);
        $this->assertInpIsGood($vitals);
        $this->assertTrue(true);
    }

    public function testAssertInpIsGoodFails(): void
    {
        $vitals = new CoreWebVitals(1000.0, 500.0, 0.05, 300.0, 0.0, 100.0, 50.0);

        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessage('INP should be below 200ms (Good threshold)');
        $this->assertInpIsGood($vitals);
    }

    public function testAssertTtfbBelowThresholdPasses(): void
    {
        $vitals = new CoreWebVitals(1000.0, 500.0, 0.05, 0.0, 0.0, 500.0, 50.0);
        $this->assertTtfbBelowThreshold($vitals, 600.0);
        $this->assertTrue(true);
    }

    public function testAssertTtfbBelowThresholdFails(): void
    {
        $vitals = new CoreWebVitals(1000.0, 500.0, 0.05, 0.0, 0.0, 1000.0, 50.0);

        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessage('TTFB 1000.00ms exceeds threshold of 600.00ms');
        $this->assertTtfbBelowThreshold($vitals, 600.0);
    }

    public function testAssertTtfbIsGoodPasses(): void
    {
        $vitals = new CoreWebVitals(1000.0, 500.0, 0.05, 0.0, 0.0, 500.0, 50.0);
        $this->assertTtfbIsGood($vitals);
        $this->assertTrue(true);
    }

    public function testAssertTtfbIsGoodFails(): void
    {
        $vitals = new CoreWebVitals(1000.0, 500.0, 0.05, 0.0, 0.0, 1000.0, 50.0);

        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessage('TTFB should be below 800ms (Good threshold)');
        $this->assertTtfbIsGood($vitals);
    }

    public function testAssertTbtBelowThresholdPasses(): void
    {
        $vitals = new CoreWebVitals(1000.0, 500.0, 0.05, 0.0, 0.0, 100.0, 50.0);
        $this->assertTbtBelowThreshold($vitals, 100.0);
        $this->assertTrue(true);
    }

    public function testAssertTbtBelowThresholdFails(): void
    {
        $vitals = new CoreWebVitals(1000.0, 500.0, 0.05, 0.0, 0.0, 100.0, 500.0);

        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessage('TBT 500.00ms exceeds threshold of 100.00ms');
        $this->assertTbtBelowThreshold($vitals, 100.0);
    }

    public function testAssertTbtIsGoodPasses(): void
    {
        $vitals = new CoreWebVitals(1000.0, 500.0, 0.05, 0.0, 0.0, 100.0, 50.0);
        $this->assertTbtIsGood($vitals);
        $this->assertTrue(true);
    }

    public function testAssertTbtIsGoodFails(): void
    {
        $vitals = new CoreWebVitals(1000.0, 500.0, 0.05, 0.0, 0.0, 100.0, 500.0);

        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessage('TBT should be below 200ms (Good threshold)');
        $this->assertTbtIsGood($vitals);
    }

    public function testAssertAllCoreWebVitalsAreGoodPasses(): void
    {
        $vitals = new CoreWebVitals(2000.0, 1500.0, 0.05, 0.0, 0.0, 500.0, 100.0);
        $this->assertAllCoreWebVitalsAreGood($vitals);
        $this->assertTrue(true);
    }

    public function testAssertAllCoreWebVitalsAreGoodFailsMultiple(): void
    {
        $vitals = new CoreWebVitals(3000.0, 2000.0, 0.5, 0.0, 0.0, 1000.0, 500.0);

        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessageMatches('/LCP: 3000\.00ms/');
        $this->expectExceptionMessageMatches('/FCP: 2000\.00ms/');
        $this->expectExceptionMessageMatches('/CLS: 0\.500/');
        $this->expectExceptionMessageMatches('/TTFB: 1000\.00ms/');
        $this->expectExceptionMessageMatches('/TBT: 500\.00ms/');
        $this->assertAllCoreWebVitalsAreGood($vitals);
    }

    public function testAssertAllCoreWebVitalsAreGoodSkipsZeroInpAndFid(): void
    {
        // INP and FID are 0, should be skipped
        $vitals = new CoreWebVitals(2000.0, 1500.0, 0.05, 0.0, 0.0, 500.0, 100.0);
        $this->assertAllCoreWebVitalsAreGood($vitals);
        $this->assertTrue(true);
    }

    public function testAssertResourceCountBelowThresholdPasses(): void
    {
        $resources = [
            new ResourceMetrics('https://example.com/script.js', 100.0, 1000, 'script', 0.0, 0.0, 0.0, 0.0, 0.0, 0.0, 0.0, 0.0, 0.0),
            new ResourceMetrics('https://example.com/style.css', 50.0, 2000, 'stylesheet', 0.0, 0.0, 0.0, 0.0, 0.0, 0.0, 0.0, 0.0, 0.0),
        ];

        $this->assertResourceCountBelowThreshold($resources, 5);
        $this->assertTrue(true);
    }

    public function testAssertResourceCountBelowThresholdFails(): void
    {
        $resources = [
            new ResourceMetrics('https://example.com/script.js', 100.0, 1000, 'script', 0.0, 0.0, 0.0, 0.0, 0.0, 0.0, 0.0, 0.0, 0.0),
            new ResourceMetrics('https://example.com/style.css', 50.0, 2000, 'stylesheet', 0.0, 0.0, 0.0, 0.0, 0.0, 0.0, 0.0, 0.0, 0.0),
            new ResourceMetrics('https://example.com/image.png', 200.0, 50000, 'image', 0.0, 0.0, 0.0, 0.0, 0.0, 0.0, 0.0, 0.0, 0.0),
        ];

        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessage('Resource count 3 exceeds threshold of 2');
        $this->assertResourceCountBelowThreshold($resources, 2);
    }

    public function testAssertTotalResourceSizeBelowThresholdPasses(): void
    {
        $resources = [
            new ResourceMetrics('https://example.com/script.js', 100.0, 1000, 'script', 0.0, 0.0, 0.0, 0.0, 0.0, 0.0, 0.0, 0.0, 0.0),
            new ResourceMetrics('https://example.com/style.css', 50.0, 2000, 'stylesheet', 0.0, 0.0, 0.0, 0.0, 0.0, 0.0, 0.0, 0.0, 0.0),
        ];

        $this->assertTotalResourceSizeBelowThreshold($resources, 5000);
        $this->assertTrue(true);
    }

    public function testAssertTotalResourceSizeBelowThresholdFails(): void
    {
        $resources = [
            new ResourceMetrics('https://example.com/script.js', 100.0, 10000, 'script', 0.0, 0.0, 0.0, 0.0, 0.0, 0.0, 0.0, 0.0, 0.0),
            new ResourceMetrics('https://example.com/style.css', 50.0, 20000, 'stylesheet', 0.0, 0.0, 0.0, 0.0, 0.0, 0.0, 0.0, 0.0, 0.0),
        ];

        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessage('Total resource size 30000 bytes exceeds threshold of 5000 bytes');
        $this->assertTotalResourceSizeBelowThreshold($resources, 5000);
    }

    public function testAssertResourceTypeSizeBelowThresholdPasses(): void
    {
        $resources = [
            new ResourceMetrics('https://example.com/script1.js', 100.0, 5000, 'script', 0.0, 0.0, 0.0, 0.0, 0.0, 0.0, 0.0, 0.0, 0.0),
            new ResourceMetrics('https://example.com/script2.js', 100.0, 5000, 'script', 0.0, 0.0, 0.0, 0.0, 0.0, 0.0, 0.0, 0.0, 0.0),
            new ResourceMetrics('https://example.com/style.css', 50.0, 20000, 'stylesheet', 0.0, 0.0, 0.0, 0.0, 0.0, 0.0, 0.0, 0.0, 0.0),
        ];

        $this->assertResourceTypeSizeBelowThreshold($resources, 'script', 15000);
        $this->assertTrue(true);
    }

    public function testAssertResourceTypeSizeBelowThresholdFails(): void
    {
        $resources = [
            new ResourceMetrics('https://example.com/script1.js', 100.0, 10000, 'script', 0.0, 0.0, 0.0, 0.0, 0.0, 0.0, 0.0, 0.0, 0.0),
            new ResourceMetrics('https://example.com/script2.js', 100.0, 10000, 'script', 0.0, 0.0, 0.0, 0.0, 0.0, 0.0, 0.0, 0.0, 0.0),
            new ResourceMetrics('https://example.com/style.css', 50.0, 5000, 'stylesheet', 0.0, 0.0, 0.0, 0.0, 0.0, 0.0, 0.0, 0.0, 0.0),
        ];

        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessage('Total script resource size 20000 bytes exceeds threshold of 15000 bytes');
        $this->assertResourceTypeSizeBelowThreshold($resources, 'script', 15000);
    }

    public function testAssertNoResourceExceedsDurationPasses(): void
    {
        $resources = [
            new ResourceMetrics('https://example.com/script.js', 100.0, 1000, 'script', 0.0, 0.0, 0.0, 0.0, 0.0, 0.0, 0.0, 0.0, 0.0),
            new ResourceMetrics('https://example.com/style.css', 50.0, 2000, 'stylesheet', 0.0, 0.0, 0.0, 0.0, 0.0, 0.0, 0.0, 0.0, 0.0),
        ];

        $this->assertNoResourceExceedsDuration($resources, 200.0);
        $this->assertTrue(true);
    }

    public function testAssertNoResourceExceedsDurationFails(): void
    {
        $resources = [
            new ResourceMetrics('https://example.com/script.js', 100.0, 1000, 'script', 0.0, 0.0, 0.0, 0.0, 0.0, 0.0, 0.0, 0.0, 0.0),
            new ResourceMetrics('https://example.com/slow.css', 500.0, 2000, 'stylesheet', 0.0, 0.0, 0.0, 0.0, 0.0, 0.0, 0.0, 0.0, 0.0),
        ];

        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessage('Resource slow.css took 500.00ms which exceeds threshold of 200.00ms');
        $this->assertNoResourceExceedsDuration($resources, 200.0);
    }
}
