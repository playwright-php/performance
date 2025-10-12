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
final class MarkdownReporter implements ReporterInterface
{
    /**
     * @param array<ResourceMetrics> $resources
     */
    public function generate(CoreWebVitals $vitals, array $resources): string
    {
        $output = "# Performance Report\n\n";
        $output .= $this->renderCoreWebVitals($vitals);
        $output .= "\n\n";
        $output .= $this->renderResourceMetrics($resources);

        return $output;
    }

    private function renderCoreWebVitals(CoreWebVitals $vitals): string
    {
        $output = "## Core Web Vitals\n\n";
        $output .= "| Metric | Value | Status |\n";
        $output .= "|--------|-------|--------|\n";

        // LCP
        $lcpSeconds = $vitals->lcp / 1000;
        $lcpStatus = $vitals->lcp < 2500 ? '✓ Good' : ($vitals->lcp < 4000 ? '⚠️ Needs Improvement' : '❌ Poor');
        $output .= sprintf("| LCP (Largest Contentful Paint) | %.2f s | %s |\n", $lcpSeconds, $lcpStatus);

        // FCP
        $fcpSeconds = $vitals->fcp / 1000;
        $fcpStatus = $vitals->fcp < 1800 ? '✓ Good' : ($vitals->fcp < 3000 ? '⚠️ Needs Improvement' : '❌ Poor');
        $output .= sprintf("| FCP (First Contentful Paint) | %.2f s | %s |\n", $fcpSeconds, $fcpStatus);

        // CLS
        $clsStatus = $vitals->cls < 0.1 ? '✓ Good' : ($vitals->cls < 0.25 ? '⚠️ Needs Improvement' : '❌ Poor');
        $output .= sprintf("| CLS (Cumulative Layout Shift) | %.3f | %s |\n", $vitals->cls, $clsStatus);

        // INP
        $inpStatus = 0.0 === $vitals->inp ? 'N/A' : ($vitals->inp < 200 ? '✓ Good' : ($vitals->inp < 500 ? '⚠️ Needs Improvement' : '❌ Poor'));
        $output .= sprintf("| INP (Interaction to Next Paint) | %.2f ms | %s |\n", $vitals->inp, $inpStatus);

        // FID
        $fidStatus = 0.0 === $vitals->fid ? 'N/A' : ($vitals->fid < 100 ? '✓ Good' : ($vitals->fid < 300 ? '⚠️ Needs Improvement' : '❌ Poor'));
        $output .= sprintf("| FID (First Input Delay) | %.2f ms | %s |\n", $vitals->fid, $fidStatus);

        // TTFB
        $ttfbStatus = $vitals->ttfb < 800 ? '✓ Good' : ($vitals->ttfb < 1800 ? '⚠️ Needs Improvement' : '❌ Poor');
        $output .= sprintf("| TTFB (Time to First Byte) | %.2f ms | %s |\n", $vitals->ttfb, $ttfbStatus);

        // TBT
        $tbtStatus = $vitals->tbt < 200 ? '✓ Good' : ($vitals->tbt < 600 ? '⚠️ Needs Improvement' : '❌ Poor');
        $output .= sprintf("| TBT (Total Blocking Time) | %.2f ms | %s |\n", $vitals->tbt, $tbtStatus);

        return $output;
    }

    /**
     * @param array<ResourceMetrics> $resources
     */
    private function renderResourceMetrics(array $resources): string
    {
        $output = "## Resource Metrics\n\n";
        $output .= sprintf("**Total Resources**: %d\n\n", count($resources));

        // Group by type
        $byType = [];
        foreach ($resources as $resource) {
            $byType[$resource->type][] = $resource;
        }

        // Summary by type
        $output .= "### Summary by Type\n\n";
        $output .= "| Type | Count | Avg Duration | Total Size |\n";
        $output .= "|------|-------|--------------|------------|\n";

        foreach ($byType as $type => $items) {
            $totalDuration = array_sum(array_map(fn ($r) => $r->duration, $items));
            $avgDuration = $totalDuration / count($items);
            $totalSize = array_sum(array_map(fn ($r) => $r->size, $items));

            $output .= sprintf(
                "| %s | %d | %.2f ms | %s |\n",
                $type,
                count($items),
                $avgDuration,
                $this->formatBytes($totalSize)
            );
        }

        // Slowest resources
        $sorted = $resources;
        usort($sorted, fn ($a, $b) => $b->duration <=> $a->duration);
        $slowest = array_slice($sorted, 0, 10);

        $output .= "\n### Slowest Resources (Top 10)\n\n";
        $output .= "| # | Duration | Type | URL |\n";
        $output .= "|---|----------|------|-----|\n";

        foreach ($slowest as $i => $resource) {
            $output .= sprintf(
                "| %d | %.2f ms | %s | %s |\n",
                $i + 1,
                $resource->duration,
                $resource->type,
                basename($resource->url)
            );
        }

        return $output;
    }

    private function formatBytes(int $bytes): string
    {
        if (0 === $bytes) {
            return '0 B';
        }

        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;
        $size = (float) $bytes;

        while ($size >= 1024 && $i < count($units) - 1) {
            $size /= 1024;
            ++$i;
        }

        return sprintf('%.2f %s', $size, $units[$i]);
    }
}
