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

namespace Playwright\Performance\Tests\Unit\Reporter;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Playwright\Performance\Metrics\CoreWebVitals;
use Playwright\Performance\Metrics\ResourceMetrics;
use Playwright\Performance\Reporter\MarkdownReporter;

#[CoversClass(MarkdownReporter::class)]
class MarkdownReporterTest extends TestCase
{
    public function testGenerateCreatesMarkdownReport(): void
    {
        $vitals = new CoreWebVitals(1000.0, 500.0, 0.05, 150.0, 50.0, 100.0, 200.0);
        $resources = [
            new ResourceMetrics('https://example.com/script.js', 100.0, 1024, 'script', 0.0, 0.0, 0.0, 0.0, 0.0, 0.0, 0.0, 0.0, 100.0),
            new ResourceMetrics('https://example.com/style.css', 50.0, 2048, 'link', 0.0, 0.0, 0.0, 0.0, 0.0, 0.0, 0.0, 0.0, 50.0),
        ];

        $reporter = new MarkdownReporter();
        $report = $reporter->generate($vitals, $resources);

        // Check for markdown headers
        $this->assertStringContainsString('# Performance Report', $report);
        $this->assertStringContainsString('## Core Web Vitals', $report);
        $this->assertStringContainsString('## Resource Metrics', $report);

        // Check for metrics
        $this->assertStringContainsString('LCP (Largest Contentful Paint)', $report);
        $this->assertStringContainsString('FCP (First Contentful Paint)', $report);
        $this->assertStringContainsString('CLS (Cumulative Layout Shift)', $report);

        // Check for resource count
        $this->assertStringContainsString('**Total Resources**: 2', $report);

        // Check for resource types
        $this->assertStringContainsString('script', $report);
        $this->assertStringContainsString('link', $report);
    }

    public function testGenerateIncludesStatusIndicators(): void
    {
        $goodVitals = new CoreWebVitals(1000.0, 500.0, 0.05, 150.0, 50.0, 100.0, 100.0);
        $reporter = new MarkdownReporter();
        $report = $reporter->generate($goodVitals, []);

        // Should have "Good" indicators
        $this->assertStringContainsString('✓ Good', $report);
    }

    public function testGenerateHandlesEmptyResources(): void
    {
        $vitals = new CoreWebVitals(1000.0, 500.0, 0.05, 0.0, 0.0, 100.0, 0.0);
        $reporter = new MarkdownReporter();
        $report = $reporter->generate($vitals, []);

        $this->assertStringContainsString('**Total Resources**: 0', $report);
    }
}
