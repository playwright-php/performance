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

use Playwright\Page\PageInterface;
use Playwright\Performance\Metrics\CoreWebVitals;
use Playwright\Performance\Metrics\ResourceMetrics;

/**
 * @phpstan-type CoreWebVitalsResult array{
 *   lcp: numeric,
 *   fcp: numeric,
 *   cls: numeric,
 *   inp: numeric,
 *   fid: numeric,
 *   tbt: numeric,
 *   ttfb: numeric,
 *  }
 * @phpstan-type ResourceEntry array{
 *    name:string,
 *    duration:int|float,
 *    transferSize:int|float,
 *    initiatorType:string,
 *    startTime:int|float,
 *    fetchStart:int|float,
 *    domainLookupStart:int|float,
 *    domainLookupEnd:int|float,
 *    connectStart:int|float,
 *    connectEnd:int|float,
 *    requestStart:int|float,
 *    responseStart:int|float,
 *    responseEnd:int|float
 *  }
 *
 * @author Simon André <smn.andre@gmail.com>
 */
final class PerformanceMonitor implements PerformanceMonitorInterface
{
    private const CORE_WEB_VITALS_SCRIPT = __DIR__.'/../../resources/js/collect-core-web-vitals.js';

    private static ?string $coreWebVitalsSnippet = null;

    public function __construct(private PageInterface $page)
    {
    }

    public function navigate(string $url): void
    {
        $this->page->goto($url, ['waitUntil' => 'load']);
    }

    public function collectCoreWebVitals(): CoreWebVitals
    {
        $script = $this->getCoreWebVitalsScript();
        $result = $this->page->evaluate($script);

        /** @var CoreWebVitalsResult $data */
        $data = $this->assertCoreWebVitals($result);

        return new CoreWebVitals(
            (float) $data['lcp'],
            (float) $data['fcp'],
            (float) $data['cls'],
            (float) $data['inp'],
            (float) $data['fid'],
            (float) $data['ttfb'],
            (float) $data['tbt']
        );
    }

    public function collectResourceMetrics(): array
    {
        $entries = $this->page->evaluate('() => performance.getEntriesByType("resource").map(entry => ({
            name: entry.name,
            duration: entry.duration,
            transferSize: entry.transferSize || entry.encodedBodySize || entry.decodedBodySize || 0,
            initiatorType: entry.initiatorType || "other",
            startTime: entry.startTime || 0,
            fetchStart: entry.fetchStart || 0,
            domainLookupStart: entry.domainLookupStart || 0,
            domainLookupEnd: entry.domainLookupEnd || 0,
            connectStart: entry.connectStart || 0,
            connectEnd: entry.connectEnd || 0,
            requestStart: entry.requestStart || 0,
            responseStart: entry.responseStart || 0,
            responseEnd: entry.responseEnd || 0
        }))');

        if (!\is_array($entries)) {
            throw new \RuntimeException('Invalid resource metrics data returned from page');
        }

        /* @var list<ResourceEntry> $entries */
        return array_map(
            static function (array $entry): ResourceMetrics {
                // keys are guaranteed by the type above
                return new ResourceMetrics(
                    $entry['name'],
                    (float) $entry['duration'],
                    (int) $entry['transferSize'],
                    $entry['initiatorType'],
                    (float) $entry['startTime'],
                    (float) $entry['fetchStart'],
                    (float) $entry['domainLookupStart'],
                    (float) $entry['domainLookupEnd'],
                    (float) $entry['connectStart'],
                    (float) $entry['connectEnd'],
                    (float) $entry['requestStart'],
                    (float) $entry['responseStart'],
                    (float) $entry['responseEnd']
                );
            },
            $entries
        );
    }

    private function getCoreWebVitalsScript(): string
    {
        if (null === self::$coreWebVitalsSnippet) {
            $contents = file_get_contents(self::CORE_WEB_VITALS_SCRIPT);
            if (false === $contents) {
                throw new \RuntimeException(sprintf('Unable to load Core Web Vitals script from "%s".', self::CORE_WEB_VITALS_SCRIPT));
            }
            self::$coreWebVitalsSnippet = $contents;
        }

        return self::$coreWebVitalsSnippet;
    }

    /**
     * @return CoreWebVitalsResult
     */
    private function assertCoreWebVitals(mixed $result): array
    {
        if (!\is_array($result)) {
            throw new \RuntimeException('Invalid Core Web Vitals data returned from page');
        }

        $keys = ['lcp', 'fcp', 'cls', 'inp', 'fid', 'tbt', 'ttfb'];
        $coreWebVitals = array_fill_keys($keys, 0.0);
        foreach ($keys as $key) {
            if (!\array_key_exists($key, $result)) {
                continue;
            }

            if (!\is_numeric($result[$key])) {
                throw new \RuntimeException('Invalid Core Web Vitals data returned from page');
            }

            $coreWebVitals[$key] = $result[$key];
        }

        return $coreWebVitals;
    }
}
