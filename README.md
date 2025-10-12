<div align="center">
<img src="https://github.com/playwright-php/.github/raw/main/profile/playwright-php.png" alt="Playwright PHP" />

&nbsp; ![PHP Version](https://img.shields.io/badge/PHP-8.2-05971B?labelColor=09161E&color=1D8D23&logoColor=FFFFFF)
&nbsp; ![CI](https://img.shields.io/github/actions/workflow/status/playwright-php/performance/CI.yaml?branch=main&label=Tests&color=1D8D23&labelColor=09161E&logoColor=FFFFFF)
&nbsp; ![Release](https://img.shields.io/github/v/release/playwright-php/performance?label=Stable&labelColor=09161E&color=1D8D23&logoColor=FFFFFF)
&nbsp; ![License](https://img.shields.io/github/license/playwright-php/performance?label=License&labelColor=09161E&color=1D8D23&logoColor=FFFFFF)

</div>

# Playwright PHP - Performance

The Performance package helps you inspect how a page behaves in a real browser 
by extracting Core Web Vitals and network timing data with a single API. 

## Features

- Capture all Core Web Vitals directly from the browser with resilient fallbacks:
    - **LCP** (Largest Contentful Paint) - Loading performance
    - **FCP** (First Contentful Paint) - Initial render timing
    - **CLS** (Cumulative Layout Shift) - Visual stability
    - **INP** (Interaction to Next Paint) - Responsiveness (Core Web Vital as of 2024)
    - **FID** (First Input Delay) - Input responsiveness
    - **TTFB** (Time to First Byte) - Server response time
    - **TBT** (Total Blocking Time) - Main thread blocking
- Collect resource timing entries and expose them as value objects for downstream analysis.

## Getting Started

### Installation

```bash
composer require --dev playwright-php/performance
```

## Usage

```php
use Playwright\Performance\Monitor\PerformanceMonitor;
use Playwright\Playwright;

$browser = Playwright::chromium();
$page = $browser->newPage();

$monitor = new PerformanceMonitor($page);
$monitor->navigate('https://example.com');

$resources = $monitor->collectResourceMetrics();

// Core Web Vitals
$vitals = $monitor->collectCoreWebVitals();

// Resource Metrics
$resources = $monitor->collectResourceMetrics();

$browser->close();
```

### Core Web Vitals

```php
// ...
// $vitals = $monitor->collectCoreWebVitals();

echo $vitals->lcp;   // Largest Contentful Paint (ms)
echo $vitals->fcp;   // First Contentful Paint (ms)
echo $vitals->cls;   // Cumulative Layout Shift
echo $vitals->inp;   // Interaction to Next Paint (ms)
echo $vitals->fid;   // First Input Delay
echo $vitals->ttfb;  // Time to First Byte (ms)
echo $vitals->tbt;   // Total Blocking Time (ms)
```

### Resources Loaded

```php
// ...
// $resources = $monitor->collectResourceMetrics();

foreach ($resources as $resource) {
    echo $resource->toArray();
}
```

### Format Results

```php
use Playwright\Performance\Reporter\JsonReporter;
use Playwright\Performance\Reporter\MarkdownReporter;

// ...
// $resources = $monitor->collectResourceMetrics();

// JSON (default)
$reporter = new JsonReporter();
file_put_contents('report.json', $reporter->generate($vitals, $resources));

// Markdown
$reporter = new MarkdownReporter();
file_put_contents('report.md', $reporter->generate($vitals, $resources));
```

## Testing

Use `MockPerformanceMonitor` to test your code without launching a browser:

```php
use Playwright\Performance\Monitor\MockPerformanceMonitor;
use Playwright\Performance\Metrics\CoreWebVitals;

class MyServiceTest extends TestCase
{
    public function testPerformanceCheck(): void
    {
        $mock = new MockPerformanceMonitor();

        // Define expected values (optional)
        $mock->setCoreWebVitals(new CoreWebVitals(100.0, 50.0, 0.01, 0.0, 0.0, 80.0, 0.0));
        $service = new MyService($mock);
        
        // No real browser is launched here
        $service->analyzePerformance('https://example.com');
    }
}
```

The package also includes a PHPUnit trait with performance assertions. See the 
full documentation for details.

## License

This package is released by the [Playwright PHP](https://playwright-php.dev) 
project under the MIT License. See the [LICENSE](LICENSE) file for details.
