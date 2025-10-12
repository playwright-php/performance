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

namespace Playwright\Performance\Metrics;

/**
 * @author Simon André <smn.andre@gmail.com>
 */
final class ResourceMetrics
{
    public function __construct(
        public readonly string $url,
        public readonly float $duration,
        public readonly int $size,
        public readonly string $type,
        public readonly float $startTime,
        public readonly float $fetchStart,
        public readonly float $domainLookupStart,
        public readonly float $domainLookupEnd,
        public readonly float $connectStart,
        public readonly float $connectEnd,
        public readonly float $requestStart,
        public readonly float $responseStart,
        public readonly float $responseEnd,
    ) {
    }

    /**
     * Get DNS lookup time in milliseconds.
     */
    public function getDnsTime(): float
    {
        if (0.0 === $this->domainLookupEnd || 0.0 === $this->domainLookupStart) {
            return 0.0;
        }

        return $this->domainLookupEnd - $this->domainLookupStart;
    }

    /**
     * Get TCP connection time in milliseconds.
     */
    public function getConnectionTime(): float
    {
        if (0.0 === $this->connectEnd || 0.0 === $this->connectStart) {
            return 0.0;
        }

        return $this->connectEnd - $this->connectStart;
    }

    /**
     * Get time to first byte in milliseconds.
     */
    public function getTtfb(): float
    {
        if (0.0 === $this->responseStart || 0.0 === $this->requestStart) {
            return 0.0;
        }

        return $this->responseStart - $this->requestStart;
    }

    /**
     * Get download time in milliseconds.
     */
    public function getDownloadTime(): float
    {
        if (0.0 === $this->responseEnd || 0.0 === $this->responseStart) {
            return 0.0;
        }

        return $this->responseEnd - $this->responseStart;
    }

    /**
     * @return array{url: string, duration: float, size: int, type: string, startTime: float, fetchStart: float, domainLookupStart: float, domainLookupEnd: float, connectStart: float, connectEnd: float, requestStart: float, responseStart: float, responseEnd: float, dnsTime: float, connectionTime: float, ttfb: float, downloadTime: float}
     */
    public function toArray(): array
    {
        return [
            'url' => $this->url,
            'duration' => $this->duration,
            'size' => $this->size,
            'type' => $this->type,
            'startTime' => $this->startTime,
            'fetchStart' => $this->fetchStart,
            'domainLookupStart' => $this->domainLookupStart,
            'domainLookupEnd' => $this->domainLookupEnd,
            'connectStart' => $this->connectStart,
            'connectEnd' => $this->connectEnd,
            'requestStart' => $this->requestStart,
            'responseStart' => $this->responseStart,
            'responseEnd' => $this->responseEnd,
            'dnsTime' => $this->getDnsTime(),
            'connectionTime' => $this->getConnectionTime(),
            'ttfb' => $this->getTtfb(),
            'downloadTime' => $this->getDownloadTime(),
        ];
    }
}
