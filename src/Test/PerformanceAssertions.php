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

namespace Playwright\Performance\Test;

use PHPUnit\Framework\Assert;
use Playwright\Performance\Metrics\CoreWebVitals;
use Playwright\Performance\Metrics\ResourceMetrics;

/**
 * Trait providing performance-related assertions for PHPUnit tests.
 *
 * @author Simon André <smn.andre@gmail.com>
 */
trait PerformanceAssertions
{
    /**
     * Assert that LCP (Largest Contentful Paint) is below the specified threshold in milliseconds.
     */
    protected function assertLcpBelowThreshold(CoreWebVitals $vitals, float $thresholdMs, string $message = ''): void
    {
        Assert::assertLessThanOrEqual(
            $thresholdMs,
            $vitals->lcp,
            $message ?: sprintf('LCP %.2fms exceeds threshold of %.2fms', $vitals->lcp, $thresholdMs)
        );
    }

    /**
     * Assert that LCP is "Good" according to Core Web Vitals thresholds (<2.5s).
     */
    protected function assertLcpIsGood(CoreWebVitals $vitals, string $message = ''): void
    {
        $this->assertLcpBelowThreshold($vitals, 2500, $message ?: 'LCP should be below 2.5s (Good threshold)');
    }

    /**
     * Assert that FCP (First Contentful Paint) is below the specified threshold in milliseconds.
     */
    protected function assertFcpBelowThreshold(CoreWebVitals $vitals, float $thresholdMs, string $message = ''): void
    {
        Assert::assertLessThanOrEqual(
            $thresholdMs,
            $vitals->fcp,
            $message ?: sprintf('FCP %.2fms exceeds threshold of %.2fms', $vitals->fcp, $thresholdMs)
        );
    }

    /**
     * Assert that FCP is "Good" according to Core Web Vitals thresholds (<1.8s).
     */
    protected function assertFcpIsGood(CoreWebVitals $vitals, string $message = ''): void
    {
        $this->assertFcpBelowThreshold($vitals, 1800, $message ?: 'FCP should be below 1.8s (Good threshold)');
    }

    /**
     * Assert that CLS (Cumulative Layout Shift) is below the specified threshold.
     */
    protected function assertClsBelowThreshold(CoreWebVitals $vitals, float $threshold, string $message = ''): void
    {
        Assert::assertLessThanOrEqual(
            $threshold,
            $vitals->cls,
            $message ?: sprintf('CLS %.3f exceeds threshold of %.3f', $vitals->cls, $threshold)
        );
    }

    /**
     * Assert that CLS is "Good" according to Core Web Vitals thresholds (<0.1).
     */
    protected function assertClsIsGood(CoreWebVitals $vitals, string $message = ''): void
    {
        $this->assertClsBelowThreshold($vitals, 0.1, $message ?: 'CLS should be below 0.1 (Good threshold)');
    }

    /**
     * Assert that INP (Interaction to Next Paint) is below the specified threshold in milliseconds.
     *
     * Note: INP requires real user interaction and may be 0 in automated tests.
     */
    protected function assertInpBelowThreshold(CoreWebVitals $vitals, float $thresholdMs, string $message = ''): void
    {
        Assert::assertLessThanOrEqual(
            $thresholdMs,
            $vitals->inp,
            $message ?: sprintf('INP %.2fms exceeds threshold of %.2fms', $vitals->inp, $thresholdMs)
        );
    }

    /**
     * Assert that INP is "Good" according to Core Web Vitals thresholds (<200ms).
     *
     * Note: INP requires real user interaction and may be 0 in automated tests.
     */
    protected function assertInpIsGood(CoreWebVitals $vitals, string $message = ''): void
    {
        $this->assertInpBelowThreshold($vitals, 200, $message ?: 'INP should be below 200ms (Good threshold)');
    }

    /**
     * Assert that FID (First Input Delay) is below the specified threshold in milliseconds.
     *
     * Note: FID requires real user interaction and may be 0 in automated tests.
     */
    protected function assertFidBelowThreshold(CoreWebVitals $vitals, float $thresholdMs, string $message = ''): void
    {
        Assert::assertLessThanOrEqual(
            $thresholdMs,
            $vitals->fid,
            $message ?: sprintf('FID %.2fms exceeds threshold of %.2fms', $vitals->fid, $thresholdMs)
        );
    }

    /**
     * Assert that FID is "Good" according to Core Web Vitals thresholds (<100ms).
     *
     * Note: FID requires real user interaction and may be 0 in automated tests.
     */
    protected function assertFidIsGood(CoreWebVitals $vitals, string $message = ''): void
    {
        $this->assertFidBelowThreshold($vitals, 100, $message ?: 'FID should be below 100ms (Good threshold)');
    }

    /**
     * Assert that TTFB (Time to First Byte) is below the specified threshold in milliseconds.
     */
    protected function assertTtfbBelowThreshold(CoreWebVitals $vitals, float $thresholdMs, string $message = ''): void
    {
        Assert::assertLessThanOrEqual(
            $thresholdMs,
            $vitals->ttfb,
            $message ?: sprintf('TTFB %.2fms exceeds threshold of %.2fms', $vitals->ttfb, $thresholdMs)
        );
    }

    /**
     * Assert that TTFB is "Good" according to Core Web Vitals thresholds (<800ms).
     */
    protected function assertTtfbIsGood(CoreWebVitals $vitals, string $message = ''): void
    {
        $this->assertTtfbBelowThreshold($vitals, 800, $message ?: 'TTFB should be below 800ms (Good threshold)');
    }

    /**
     * Assert that TBT (Total Blocking Time) is below the specified threshold in milliseconds.
     */
    protected function assertTbtBelowThreshold(CoreWebVitals $vitals, float $thresholdMs, string $message = ''): void
    {
        Assert::assertLessThanOrEqual(
            $thresholdMs,
            $vitals->tbt,
            $message ?: sprintf('TBT %.2fms exceeds threshold of %.2fms', $vitals->tbt, $thresholdMs)
        );
    }

    /**
     * Assert that TBT is "Good" according to Core Web Vitals thresholds (<200ms).
     */
    protected function assertTbtIsGood(CoreWebVitals $vitals, string $message = ''): void
    {
        $this->assertTbtBelowThreshold($vitals, 200, $message ?: 'TBT should be below 200ms (Good threshold)');
    }

    /**
     * Assert that all Core Web Vitals are "Good" according to recommended thresholds.
     *
     * Note: INP and FID may be 0 in automated tests and will be skipped if so.
     */
    protected function assertAllCoreWebVitalsAreGood(CoreWebVitals $vitals, string $message = ''): void
    {
        $failures = [];

        if ($vitals->lcp >= 2500) {
            $failures[] = sprintf('LCP: %.2fms (should be < 2500ms)', $vitals->lcp);
        }

        if ($vitals->fcp >= 1800) {
            $failures[] = sprintf('FCP: %.2fms (should be < 1800ms)', $vitals->fcp);
        }

        if ($vitals->cls >= 0.1) {
            $failures[] = sprintf('CLS: %.3f (should be < 0.1)', $vitals->cls);
        }

        // Only check INP/FID if they have values
        if ($vitals->inp >= 200) {
            $failures[] = sprintf('INP: %.2fms (should be < 200ms)', $vitals->inp);
        }

        if ($vitals->fid >= 100) {
            $failures[] = sprintf('FID: %.2fms (should be < 100ms)', $vitals->fid);
        }

        if ($vitals->ttfb >= 800) {
            $failures[] = sprintf('TTFB: %.2fms (should be < 800ms)', $vitals->ttfb);
        }

        if ($vitals->tbt >= 200) {
            $failures[] = sprintf('TBT: %.2fms (should be < 200ms)', $vitals->tbt);
        }

        if (count($failures) > 0) {
            Assert::fail(
                ($message ?: 'Core Web Vitals failed:')."\n  - ".implode("\n  - ", $failures)
            );
        }

        Assert::assertTrue(true); // Explicitly pass if no failures
    }

    /**
     * Assert that the total number of resources is below the specified threshold.
     *
     * @param array<ResourceMetrics> $resources
     */
    protected function assertResourceCountBelowThreshold(array $resources, int $threshold, string $message = ''): void
    {
        Assert::assertLessThanOrEqual(
            $threshold,
            count($resources),
            $message ?: sprintf('Resource count %d exceeds threshold of %d', count($resources), $threshold)
        );
    }

    /**
     * Assert that the total size of all resources is below the specified threshold in bytes.
     *
     * @param array<ResourceMetrics> $resources
     */
    protected function assertTotalResourceSizeBelowThreshold(array $resources, int $thresholdBytes, string $message = ''): void
    {
        $totalSize = array_sum(array_map(fn (ResourceMetrics $r) => $r->size, $resources));

        Assert::assertLessThanOrEqual(
            $thresholdBytes,
            $totalSize,
            $message ?: sprintf('Total resource size %d bytes exceeds threshold of %d bytes', $totalSize, $thresholdBytes)
        );
    }

    /**
     * Assert that a specific resource type's total size is below the specified threshold in bytes.
     *
     * @param array<ResourceMetrics> $resources
     */
    protected function assertResourceTypeSizeBelowThreshold(array $resources, string $type, int $thresholdBytes, string $message = ''): void
    {
        $filtered = array_filter($resources, fn (ResourceMetrics $r) => $r->type === $type);
        $totalSize = array_sum(array_map(fn (ResourceMetrics $r) => $r->size, $filtered));

        Assert::assertLessThanOrEqual(
            $thresholdBytes,
            $totalSize,
            $message ?: sprintf('Total %s resource size %d bytes exceeds threshold of %d bytes', $type, $totalSize, $thresholdBytes)
        );
    }

    /**
     * Assert that no single resource exceeds the specified duration threshold in milliseconds.
     *
     * @param array<ResourceMetrics> $resources
     */
    protected function assertNoResourceExceedsDuration(array $resources, float $thresholdMs, string $message = ''): void
    {
        foreach ($resources as $resource) {
            if ($resource->duration > $thresholdMs) {
                Assert::fail(
                    $message ?: sprintf(
                        'Resource %s took %.2fms which exceeds threshold of %.2fms',
                        basename($resource->url),
                        $resource->duration,
                        $thresholdMs
                    )
                );
            }
        }

        Assert::assertTrue(true);
    }
}
