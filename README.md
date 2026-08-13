<div align="center">
<a href="https://github.com/playwright-php"><img src="https://github.com/playwright-php/.github/raw/main/profile/playwright-php.png" alt="Playwright PHP" /></a>

&nbsp; ![PHP Version](https://img.shields.io/badge/PHP-8.2+-05971B?labelColor=09161E&color=1D8D23&logoColor=FFFFFF)
&nbsp; ![CI](https://img.shields.io/github/actions/workflow/status/playwright-php/performance/CI.yml?branch=main&label=Tests&color=1D8D23&labelColor=09161E&logoColor=FFFFFF)
&nbsp; [![Release](https://img.shields.io/github/v/release/playwright-php/performance?label=Stable&labelColor=09161E&color=1D8D23&logoColor=FFFFFF)](https://packagist.org/packages/playwright-php/performance)
&nbsp; ![License](https://img.shields.io/github/license/playwright-php/performance?label=License&labelColor=09161E&color=1D8D23&logoColor=FFFFFF)

</div>

# Playwright PHP Performance

Collect Core Web Vitals and resource timing data from pages opened with Playwright PHP.

## Installation

```bash
composer require --dev playwright-php/performance
vendor/bin/playwright-install --browsers
```

The package requires PHP 8.2+ and Playwright PHP 1.x.

## Quick Start

Navigate to a page, collect its metrics, and generate a report:

```php
<?php

require __DIR__.'/vendor/autoload.php';

use Playwright\Performance\Monitor\PerformanceMonitor;
use Playwright\Performance\Reporter\JsonReporter;
use Playwright\Playwright;

$context = Playwright::chromium();
$page = $context->newPage();
$monitor = new PerformanceMonitor($page);

$monitor->navigate('https://example.com');

$vitals = $monitor->collectCoreWebVitals();
$resources = $monitor->collectResourceMetrics();
$report = (new JsonReporter())->generate($vitals, $resources);

file_put_contents('performance.json', $report);

$context->close();
```

`CoreWebVitals` exposes LCP, FCP, CLS, INP, FID, TTFB, and TBT as public readonly properties and through getter methods.

Each `ResourceMetrics` value contains the URL, resource type, duration, transfer size, and detailed network timing values.

## PHPUnit Assertions

Use `PerformanceAssertions` with the core Playwright test case to enforce broad, stable budgets:

```php
use Playwright\Performance\Monitor\PerformanceMonitor;
use Playwright\Performance\Test\PerformanceAssertions;
use Playwright\Testing\PlaywrightTestCase;

final class HomepagePerformanceTest extends PlaywrightTestCase
{
    use PerformanceAssertions;

    public function testHomepageBudgets(): void
    {
        $monitor = new PerformanceMonitor($this->page);
        $monitor->navigate('https://example.com');

        $vitals = $monitor->collectCoreWebVitals();
        $resources = $monitor->collectResourceMetrics();

        $this->assertLcpBelowThreshold($vitals, 2500);
        $this->assertClsBelowThreshold($vitals, 0.1);
        $this->assertResourceCountBelowThreshold($resources, 50);
    }
}
```

Shared CI runners vary. Use budgets that detect meaningful regressions instead of asserting exact timings.

## Reports

`JsonReporter` produces structured JSON. `MarkdownReporter` produces a readable summary with Core Web Vitals and the slowest resources.

```php
use Playwright\Performance\Reporter\MarkdownReporter;

$markdown = (new MarkdownReporter())->generate($vitals, $resources);
file_put_contents('performance.md', $markdown);
```

## Testing Without a Browser

`MockPerformanceMonitor` implements the same interface as the browser-backed monitor:

```php
use Playwright\Performance\Metrics\CoreWebVitals;
use Playwright\Performance\Monitor\MockPerformanceMonitor;

$monitor = new MockPerformanceMonitor();
$monitor->setCoreWebVitals(new CoreWebVitals(
    lcp: 1000.0,
    fcp: 500.0,
    cls: 0.05,
    inp: 0.0,
    fid: 0.0,
    ttfb: 100.0,
    tbt: 0.0,
));
```

## Limits

- Automated browser metrics are laboratory data, not real-user monitoring.
- INP and FID require user interaction and may remain zero in page-load tests.
- Browser and runner variance can make narrow timing thresholds unreliable.

## Documentation

- [Playwright PHP Getting Started](https://github.com/playwright-php/playwright/blob/main/docs/guide/getting-started.md)

## Contributing

Contributions are welcome. Before submitting a pull request, run:

```bash
composer validate --strict
vendor/bin/php-cs-fixer fix --dry-run --diff
vendor/bin/phpstan analyse
vendor/bin/phpunit
```

## License

Playwright PHP Performance is released under the [MIT License](LICENSE).
