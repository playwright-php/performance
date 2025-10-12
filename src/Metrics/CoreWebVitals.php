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
final class CoreWebVitals
{
    public function __construct(
        public readonly float $lcp,
        public readonly float $fcp,
        public readonly float $cls,
        public readonly float $inp,
        public readonly float $fid,
        public readonly float $ttfb,
        public readonly float $tbt,
    ) {
    }

    /**
     * Return collected LCP (Largest Contentful Paint).
     */
    public function getLCP(): float
    {
        return $this->lcp;
    }

    /**
     * Return collected FCP (First Contentful Paint).
     */
    public function getFCP(): float
    {
        return $this->fcp;
    }

    /**
     * Return collected CLS (Cumulative Layout Shift).
     */
    public function getCLS(): float
    {
        return $this->cls;
    }

    /**
     * Return collected INP (Interaction to Next Paint).
     */
    public function getINP(): float
    {
        return $this->inp;
    }

    /**
     * Return collected FID (First Input Delay).
     */
    public function getFID(): float
    {
        return $this->fid;
    }

    /**
     * Return collected TTFB (Time to First Byte).
     */
    public function getTTFB(): float
    {
        return $this->ttfb;
    }

    /**
     * Return collected TBT (Total Blocking Time).
     */
    public function getTBT(): float
    {
        return $this->tbt;
    }

    /**
     * @return array{lcp: float, fcp: float, cls: float, inp: float, fid: float, ttfb: float, tbt: float}
     */
    public function toArray(): array
    {
        return [
            'lcp' => $this->lcp,
            'fcp' => $this->fcp,
            'cls' => $this->cls,
            'inp' => $this->inp,
            'fid' => $this->fid,
            'ttfb' => $this->ttfb,
            'tbt' => $this->tbt,
        ];
    }
}
