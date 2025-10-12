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

namespace Playwright\Performance\Reporter;

use Playwright\Performance\Metrics\CoreWebVitals;
use Playwright\Performance\Metrics\ResourceMetrics;

/**
 * @author Simon André <smn.andre@gmail.com>
 */
final class JsonReporter implements ReporterInterface
{
    /**
     * @param array<ResourceMetrics> $resources
     */
    public function generate(CoreWebVitals $vitals, array $resources): string
    {
        $report = [
            'core_web_vitals' => $vitals->toArray(),
            'resources' => array_map(
                static fn (ResourceMetrics $resource): array => $resource->toArray(),
                $resources
            ),
        ];

        return json_encode($report, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT);
    }
}
