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
 * @author Simon André <smn.andre@gmail.com>
 */
interface PerformanceMonitorInterface
{
    /**
     * Navigate to URL and wait for load.
     */
    public function navigate(string $url): void;

    /**
     * Collect Core Web Vitals metrics.
     */
    public function collectCoreWebVitals(): CoreWebVitals;

    /**
     * Collect resource timing metrics.
     *
     * @return array<ResourceMetrics>
     */
    public function collectResourceMetrics(): array;
}
