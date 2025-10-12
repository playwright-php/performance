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
use Playwright\Performance\Reporter\JsonReporter;

#[CoversClass(JsonReporter::class)]
class JsonReporterTest extends TestCase
{
    public function testGenerate(): void
    {
        $vitals = new CoreWebVitals(1.0, 2.0, 0.1, 150.0, 50.0, 100.0, 200.0);
        $resources = [new ResourceMetrics(
            'https://example.com',
            100.0,
            1024,
            'script',
            0.0,
            0.0,
            0.0,
            0.0,
            0.0,
            0.0,
            0.0,
            0.0,
            100.0
        )];

        $reporter = new JsonReporter();
        $report = $reporter->generate($vitals, $resources);

        $this->assertJson($report);

        $decodedReport = json_decode($report, true);
        $this->assertIsArray($decodedReport);
        $this->assertIsArray($decodedReport['core_web_vitals'] ?? null);
        $this->assertIsArray($decodedReport['resources'] ?? null);
        $this->assertIsArray($decodedReport['resources'][0] ?? null);

        $this->assertEquals(1.0, $decodedReport['core_web_vitals']['lcp']);
        $this->assertEquals(2.0, $decodedReport['core_web_vitals']['fcp']);
        $this->assertEquals(0.1, $decodedReport['core_web_vitals']['cls']);
        $this->assertEquals(150.0, $decodedReport['core_web_vitals']['inp']);
        $this->assertEquals(50.0, $decodedReport['core_web_vitals']['fid']);
        $this->assertEquals(100.0, $decodedReport['core_web_vitals']['ttfb']);
        $this->assertEquals(200.0, $decodedReport['core_web_vitals']['tbt']);
        $this->assertEquals('https://example.com', $decodedReport['resources'][0]['url']);
        $this->assertEquals(100.0, $decodedReport['resources'][0]['duration']);
        $this->assertEquals(1024, $decodedReport['resources'][0]['size']);
    }
}
