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

namespace Playwright\Performance\Monitor;

use Playwright\Performance\Metrics\CoreWebVitals;
use Playwright\Performance\Metrics\ResourceMetrics;

/**
 * Mock implementation of PerformanceMonitorInterface for testing.
 *
 * @author Simon André <smn.andre@gmail.com>
 */
final class MockPerformanceMonitor implements PerformanceMonitorInterface
{
    private ?CoreWebVitals $coreWebVitals = null;

    /** @var array<ResourceMetrics> */
    private array $resourceMetrics = [];

    /** @var array<string> */
    private array $navigatedUrls = [];

    /**
     * Set the Core Web Vitals that will be returned by collectCoreWebVitals().
     */
    public function setCoreWebVitals(CoreWebVitals $vitals): void
    {
        $this->coreWebVitals = $vitals;
    }

    /**
     * Set the resource metrics that will be returned by collectResourceMetrics().
     *
     * @param array<ResourceMetrics> $resources
     */
    public function setResourceMetrics(array $resources): void
    {
        $this->resourceMetrics = $resources;
    }

    /**
     * Get the list of URLs that were navigated to.
     *
     * @return array<string>
     */
    public function getNavigatedUrls(): array
    {
        return $this->navigatedUrls;
    }

    public function navigate(string $url): void
    {
        $this->navigatedUrls[] = $url;
    }

    public function collectCoreWebVitals(): CoreWebVitals
    {
        // Return default "good" metrics if none set
        return $this->coreWebVitals ??= new CoreWebVitals(
            1000.0,  // LCP
            500.0,   // FCP
            0.05,    // CLS
            0.0,     // INP
            0.0,     // FID
            100.0,   // TTFB
            0.0      // TBT
        );
    }

    /**
     * @return ResourceMetrics[]
     */
    public function collectResourceMetrics(): array
    {
        return $this->resourceMetrics;
    }
}
