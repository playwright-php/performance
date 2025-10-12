() => new Promise((resolve) => {
    if (typeof PerformanceObserver === 'undefined') {
        resolve({lcp: 0, fcp: 0, cls: 0, inp: 0, fid: 0, ttfb: 0, tbt: 0});
        return;
    }

    const vitals = {lcp: 0, fcp: 0, cls: 0, inp: 0, fid: 0, ttfb: 0, tbt: 0};
    let lcpFound = false;
    let fcpFound = false;
    let clsFound = false;
    let inpFound = false;
    let fidFound = false;
    let ttfbFound = false;
    let tbtFound = false;
    let resolved = false;
    const observers = [];
    const interactionDelays = [];

    const disconnectAll = () => {
        observers.forEach((observer) => observer.disconnect());
        observers.length = 0;
    };

    const finalize = () => {
        if (resolved) {
            return;
        }
        resolved = true;
        disconnectAll();
        resolve(vitals);
    };

    const checkComplete = () => {
        if (lcpFound && fcpFound && clsFound && inpFound && fidFound && ttfbFound && tbtFound) {
            finalize();
        }
    };

    const lcpObserver = new PerformanceObserver((list) => {
        const entries = list.getEntries();
        if (entries.length > 0) {
            const entry = entries[entries.length - 1];
            // Use renderTime if available, otherwise use loadTime
            vitals.lcp = entry.renderTime || entry.loadTime || entry.startTime;
            lcpFound = true;
            checkComplete();
        }
    });
    lcpObserver.observe({type: 'largest-contentful-paint', buffered: true});
    observers.push(lcpObserver);

    const paintObserver = new PerformanceObserver((list) => {
        for (const entry of list.getEntries()) {
            if (entry.name === 'first-contentful-paint') {
                vitals.fcp = entry.startTime;
                fcpFound = true;
                checkComplete();
                return;
            }
        }
    });
    paintObserver.observe({type: 'paint', buffered: true});
    observers.push(paintObserver);

    const markClsSettled = () => {
        if (!clsFound) {
            clsFound = true;
            checkComplete();
        }
    };

    const clsObserver = new PerformanceObserver((list) => {
        const entries = list.getEntries();
        if (entries.length > 0) {
            for (const entry of entries) {
                if (!entry.hadRecentInput) {
                    vitals.cls += entry.value;
                }
            }
            markClsSettled();
        }
    });
    clsObserver.observe({type: 'layout-shift', buffered: true});
    observers.push(clsObserver);

    // INP (Interaction to Next Paint) Observer
    try {
        const inpObserver = new PerformanceObserver((list) => {
            const entries = list.getEntries();
            for (const entry of entries) {
                if (entry.duration > vitals.inp) {
                    vitals.inp = entry.duration;
                }
            }
            if (!inpFound) {
                inpFound = true;
                checkComplete();
            }
        });
        inpObserver.observe({type: 'event', buffered: true, durationThreshold: 16});
        observers.push(inpObserver);
    } catch (e) {
        // INP not supported, fallback to 0
        inpFound = true;
    }

    // FID (First Input Delay) Observer
    try {
        const fidObserver = new PerformanceObserver((list) => {
            const entries = list.getEntries();
            if (entries.length > 0 && !fidFound) {
                const firstInput = entries[0];
                vitals.fid = firstInput.processingStart - firstInput.startTime;
                fidFound = true;
                checkComplete();
            }
        });
        fidObserver.observe({type: 'first-input', buffered: true});
        observers.push(fidObserver);
    } catch (e) {
        // FID not supported, fallback to 0
        fidFound = true;
    }

    // TTFB (Time to First Byte) - from Navigation Timing
    const navTiming = performance.getEntriesByType('navigation')[0];
    if (navTiming && navTiming.responseStart) {
        vitals.ttfb = navTiming.responseStart;
        ttfbFound = true;
    } else {
        ttfbFound = true;
    }

    // TBT (Total Blocking Time) - estimate from long tasks
    let tbtAccumulator = 0;
    try {
        const tbtObserver = new PerformanceObserver((list) => {
            const entries = list.getEntries();
            for (const entry of entries) {
                if (entry.duration > 50) {
                    tbtAccumulator += entry.duration - 50;
                }
            }
            vitals.tbt = tbtAccumulator;
            if (!tbtFound) {
                tbtFound = true;
                checkComplete();
            }
        });
        tbtObserver.observe({type: 'longtask', buffered: true});
        observers.push(tbtObserver);
    } catch (e) {
        // Long tasks not supported, fallback to 0
        tbtFound = true;
    }

    const existingLcpEntries = performance.getEntriesByType('largest-contentful-paint');
    if (existingLcpEntries.length > 0) {
        const latest = existingLcpEntries[existingLcpEntries.length - 1];
        vitals.lcp = latest.renderTime || latest.loadTime || latest.startTime;
        lcpFound = true;
    }

    const existingFcp = performance.getEntriesByName('first-contentful-paint')[0];
    if (existingFcp) {
        vitals.fcp = existingFcp.startTime;
        fcpFound = true;
    }

    const existingLayoutShifts = performance.getEntriesByType('layout-shift');
    if (existingLayoutShifts.length > 0) {
        for (const entry of existingLayoutShifts) {
            if (!entry.hadRecentInput) {
                vitals.cls += entry.value;
            }
        }
        markClsSettled();
    }

    document.addEventListener('visibilitychange', () => {
        if (document.visibilityState === 'hidden') {
            markClsSettled();
        }
    }, {once: true});
    window.addEventListener('pagehide', markClsSettled, {once: true});

    requestAnimationFrame(() => {
        requestAnimationFrame(markClsSettled);
    });

    setTimeout(() => {
        if (!lcpFound) {
            const fallbackLcp = performance.getEntriesByType('navigation')[0];
            vitals.lcp = fallbackLcp ? fallbackLcp.loadEventEnd : performance.now();
            lcpFound = true;
        }
        if (!fcpFound) {
            const fallbackFcp = performance.getEntriesByName('first-contentful-paint')[0];
            vitals.fcp = fallbackFcp ? fallbackFcp.startTime : performance.now();
            fcpFound = true;
        }
        markClsSettled();

        // Mark interaction metrics as settled after timeout
        // These require user interaction and may not fire on page load
        if (!inpFound) {
            inpFound = true;
        }
        if (!fidFound) {
            fidFound = true;
        }
        if (!tbtFound) {
            tbtFound = true;
        }

        // Force finalize after timeout
        finalize();
    }, 5000);

    checkComplete();
})
