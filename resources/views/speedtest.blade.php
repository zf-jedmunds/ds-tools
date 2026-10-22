@extends('layouts.app')

@section('title', 'Speedtest')

@section('styles')
    :root {
        --dl: var(--accent);
        --ul: #3dd6b0;
    }

    .main {
        flex: 1;
        display: flex;
        flex-direction: column;
        align-items: center;
        padding: 2.5rem 2rem 3rem;
        gap: 2rem;
    }

    .target-bar {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        font-size: 0.7rem;
        color: var(--text-secondary);
        border: 1px solid var(--border);
        background: var(--bg-panel);
        padding: 0.5rem 1rem;
    }

    .target-bar .status-dot {
        width: 7px;
        height: 7px;
        border-radius: 50%;
        background: var(--text-dim);
        display: inline-block;
    }

    .target-bar.testing .status-dot {
        background: var(--accent);
        box-shadow: 0 0 8px rgba(87, 64, 153, 0.6);
        animation: pulse 1s ease-in-out infinite;
    }

    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.35; }
    }

    .target-bar strong {
        color: var(--text-primary);
        font-weight: 600;
    }

    /* Gauge */
    .gauge-wrap {
        position: relative;
        width: 100%;
        max-width: 460px;
    }

    .gauge-wrap svg {
        width: 100%;
        height: auto;
        display: block;
    }

    .gauge-track {
        fill: none;
        stroke: var(--border);
        stroke-width: 10;
        stroke-linecap: round;
    }

    .gauge-progress {
        fill: none;
        stroke: var(--dl);
        stroke-width: 10;
        stroke-linecap: round;
        transition: stroke 0.3s ease;
    }

    .gauge-tick {
        stroke: var(--text-dim);
        stroke-width: 1.5;
    }

    .gauge-tick-label {
        fill: var(--text-dim);
        font-family: 'JetBrains Mono', monospace;
        font-size: 11px;
        text-anchor: middle;
        dominant-baseline: middle;
    }

    .gauge-needle {
        stroke: var(--text-primary);
        stroke-width: 3;
        stroke-linecap: round;
        transition: transform 0.12s linear;
        transform-box: view-box;
        transform-origin: 200px 200px;
    }

    .gauge-hub {
        fill: var(--bg-elevated);
        stroke: var(--border-focus);
        stroke-width: 2;
    }

    .gauge-readout {
        position: absolute;
        top: 58%;
        left: 50%;
        transform: translate(-50%, -50%);
        text-align: center;
        pointer-events: none;
    }

    .gauge-value {
        font-family: 'Outfit', sans-serif;
        font-size: 3.2rem;
        font-weight: 800;
        line-height: 1;
        letter-spacing: -2px;
        color: var(--text-primary);
    }

    .gauge-unit {
        font-size: 0.75rem;
        color: var(--text-secondary);
        letter-spacing: 0.15em;
        text-transform: uppercase;
        margin-top: 0.35rem;
    }

    .gauge-phase {
        font-size: 0.68rem;
        letter-spacing: 0.2em;
        text-transform: uppercase;
        font-weight: 600;
        margin-top: 0.6rem;
        min-height: 1rem;
        color: var(--text-dim);
    }

    .gauge-phase.dl { color: var(--dl); }
    .gauge-phase.ul { color: var(--ul); }

    /* Result tiles */
    .results {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 1px;
        background: var(--border);
        border: 1px solid var(--border);
        width: 100%;
        max-width: 640px;
    }

    .result {
        background: var(--bg-panel);
        padding: 1.25rem 1rem;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 0.4rem;
    }

    .result-label {
        display: flex;
        align-items: center;
        gap: 0.4rem;
        font-size: 0.62rem;
        letter-spacing: 0.12em;
        text-transform: uppercase;
        color: var(--text-dim);
        font-weight: 600;
    }

    .result-icon { font-size: 0.85rem; line-height: 1; }
    .result.dl .result-icon { color: var(--dl); }
    .result.ul .result-icon { color: var(--ul); }

    .result-value {
        font-family: 'Outfit', sans-serif;
        font-size: 1.6rem;
        font-weight: 700;
        letter-spacing: -0.5px;
        color: var(--text-primary);
    }

    .result-value .u {
        font-size: 0.7rem;
        color: var(--text-secondary);
        font-weight: 500;
        margin-left: 0.2rem;
    }

    .result-value.pending { color: var(--text-dim); }

    .hint {
        font-size: 0.68rem;
        color: var(--text-dim);
        text-align: center;
        line-height: 1.7;
        max-width: 460px;
    }

    @media (max-width: 640px) {
        .results { grid-template-columns: repeat(2, 1fr); }
        .gauge-value { font-size: 2.6rem; }
    }
@endsection

@section('actions')
    <button class="btn btn-primary" id="btn-start" onclick="startTest()">start test</button>
    <button class="btn btn-danger" id="btn-abort" onclick="abortTest()" disabled>abort</button>
@endsection

@section('content')
    <main class="main">
        <div class="target-bar" id="target-bar">
            <span class="status-dot"></span>
            <span>testing against <strong id="target-host"></strong></span>
        </div>

        <div class="gauge-wrap">
            <svg viewBox="0 0 400 230" aria-hidden="true">
                <path class="gauge-track" id="gauge-track"></path>
                <path class="gauge-progress" id="gauge-progress"></path>
                <g id="gauge-ticks"></g>
                <line class="gauge-needle" id="gauge-needle" x1="200" y1="200" x2="200" y2="62"></line>
                <circle class="gauge-hub" cx="200" cy="200" r="9"></circle>
            </svg>
            <div class="gauge-readout">
                <div class="gauge-value" id="gauge-value">0.00</div>
                <div class="gauge-unit">Mbps</div>
                <div class="gauge-phase" id="gauge-phase">ready</div>
            </div>
        </div>

        <div class="results">
            <div class="result dl">
                <div class="result-label"><span class="result-icon">&#8595;</span> download</div>
                <div class="result-value pending" id="res-dl">&mdash;<span class="u">Mbps</span></div>
            </div>
            <div class="result ul">
                <div class="result-label"><span class="result-icon">&#8593;</span> upload</div>
                <div class="result-value pending" id="res-ul">&mdash;<span class="u">Mbps</span></div>
            </div>
            <div class="result">
                <div class="result-label">ping</div>
                <div class="result-value pending" id="res-ping">&mdash;<span class="u">ms</span></div>
            </div>
            <div class="result">
                <div class="result-label">jitter</div>
                <div class="result-value pending" id="res-jitter">&mdash;<span class="u">ms</span></div>
            </div>
        </div>

        <p class="hint">
            Runs entirely between your browser and this server &mdash; no third-party servers involved.
            Results depend on your network path, the server host, and browser overhead.
        </p>
    </main>
@endsection

@section('scripts')
    <script>
        const CONFIG = {
            pingCount: 12,
            dlDuration: 10,       // seconds
            ulDuration: 10,       // seconds
            dlStreams: 6,
            ulStreams: 3,
            dlChunkBytes: 104857600,  // 100 MiB per download request (fewer re-request stalls)
            ulChunkBytes: 4194304,    // 4 MiB per upload request (safe under post_max_size)
            graceMs: 1500,            // warm-up skipped before measuring (TCP slow-start / burst)
            windowMs: 1000,           // sliding window for the live gauge reading
            minWindowMs: 700,         // don't display a reading until the window is this wide
            smoothing: 0.2,           // EMA factor for the live needle (lower = smoother)
            maxScale: 1000,           // gauge top of scale, Mbps
        };

        const CX = 200, CY = 200, R = 150;

        // ---- Gauge geometry (log scale) ----
        function frac(mbps) {
            if (mbps <= 0) return 0;
            const f = Math.log10(mbps + 1) / Math.log10(CONFIG.maxScale + 1);
            return Math.max(0, Math.min(1, f));
        }
        function polar(r, f) {
            const deg = -90 + 180 * f;
            const rad = deg * Math.PI / 180;
            return { x: CX + r * Math.sin(rad), y: CY - r * Math.cos(rad) };
        }
        function arcPath(r, f0, f1) {
            if (f1 <= f0) return '';
            const a = polar(r, f0), b = polar(r, f1);
            // The gauge spans 180°, so any sub-arc is always <= 180° — i.e. the
            // minor arc. large-arc-flag must stay 0; setting it to 1 makes the
            // browser draw the major arc off the opposite centre, bulging the
            // stroke outside the track.
            const large = 0;
            return `M ${a.x.toFixed(2)} ${a.y.toFixed(2)} A ${r} ${r} 0 ${large} 1 ${b.x.toFixed(2)} ${b.y.toFixed(2)}`;
        }

        const els = {
            track: document.getElementById('gauge-track'),
            progress: document.getElementById('gauge-progress'),
            ticks: document.getElementById('gauge-ticks'),
            needle: document.getElementById('gauge-needle'),
            value: document.getElementById('gauge-value'),
            phase: document.getElementById('gauge-phase'),
            targetBar: document.getElementById('target-bar'),
            btnStart: document.getElementById('btn-start'),
            btnAbort: document.getElementById('btn-abort'),
        };

        // Build static gauge parts
        els.track.setAttribute('d', arcPath(R, 0, 1));
        [0, 1, 10, 100, 1000].forEach(v => {
            const f = frac(v);
            const inner = polar(R - 14, f);
            const outer = polar(R - 2, f);
            const label = polar(R + 16, f);
            const line = document.createElementNS('http://www.w3.org/2000/svg', 'line');
            line.setAttribute('class', 'gauge-tick');
            line.setAttribute('x1', inner.x.toFixed(2));
            line.setAttribute('y1', inner.y.toFixed(2));
            line.setAttribute('x2', outer.x.toFixed(2));
            line.setAttribute('y2', outer.y.toFixed(2));
            els.ticks.appendChild(line);
            const text = document.createElementNS('http://www.w3.org/2000/svg', 'text');
            text.setAttribute('class', 'gauge-tick-label');
            text.setAttribute('x', label.x.toFixed(2));
            text.setAttribute('y', label.y.toFixed(2));
            text.textContent = v >= 1000 ? '1k' : String(v);
            els.ticks.appendChild(text);
        });

        function setGauge(mbps, kind) {
            const f = frac(mbps);
            els.needle.style.transform = `rotate(${(-90 + 180 * f).toFixed(2)}deg)`;
            els.progress.setAttribute('d', arcPath(R, 0, f));
            const color = kind === 'ul' ? 'var(--ul)' : 'var(--dl)';
            els.progress.style.stroke = color;
            els.value.textContent = mbps >= 100 ? mbps.toFixed(1) : mbps.toFixed(2);
        }

        function setPhase(text, kind) {
            els.phase.textContent = text;
            els.phase.className = 'gauge-phase' + (kind ? ' ' + kind : '');
        }

        // ---- Test state ----
        let running = false;
        let aborted = false;
        let controllers = [];

        function newController() {
            const c = new AbortController();
            controllers.push(c);
            return c;
        }

        // Live gauge driver with a warm-up grace period (skips TCP slow-start /
        // the initial buffered burst) and a sliding-window reading so the needle
        // shows a stable recent rate instead of spiking on the first bytes.
        // getBytes() returns cumulative bytes transferred so far.
        function makeMeter(kind, getBytes) {
            const overallStart = performance.now();
            let baseBytes = 0, measureStart = 0, gracePassed = false;
            let samples = [{ t: overallStart, b: 0 }];
            let displayed = null; // EMA-smoothed value shown on the gauge

            const ticker = setInterval(() => {
                const now = performance.now();
                const bytes = getBytes();
                if (!gracePassed && now - overallStart >= CONFIG.graceMs) {
                    gracePassed = true;
                    baseBytes = bytes;
                    measureStart = now;
                    samples = [];
                }
                samples.push({ t: now, b: bytes });
                while (samples.length > 1 && now - samples[0].t > CONFIG.windowMs) samples.shift();
                const dt = (now - samples[0].t) / 1000;
                if (dt * 1000 >= CONFIG.minWindowMs) {
                    const rate = (bytes - samples[0].b) * 8 / dt / 1e6;
                    displayed = displayed === null ? rate : displayed + (rate - displayed) * CONFIG.smoothing;
                    setGauge(displayed, kind);
                }
            }, 100);

            return {
                stop() {
                    clearInterval(ticker);
                    const now = performance.now();
                    const bytes = getBytes();
                    const dur = (now - measureStart) / 1000;
                    const mbps = (gracePassed && dur > 0)
                        ? (bytes - baseBytes) * 8 / dur / 1e6
                        : bytes * 8 / ((now - overallStart) / 1000) / 1e6;
                    setGauge(mbps, kind);
                    return mbps;
                },
            };
        }

        // ---- Ping ----
        async function pingTest() {
            setPhase('ping', null);
            const rtts = [];
            for (let i = 0; i < CONFIG.pingCount; i++) {
                if (aborted) break;
                const t0 = performance.now();
                try {
                    await fetch('/speedtest/ping?r=' + Math.random(), {
                        cache: 'no-store',
                        signal: newController().signal,
                    });
                } catch (e) {
                    if (aborted) break;
                    continue;
                }
                rtts.push(performance.now() - t0);
            }
            if (!rtts.length) return { ping: 0, jitter: 0 };
            const ping = Math.min(...rtts);
            let jitter = 0;
            for (let i = 1; i < rtts.length; i++) jitter += Math.abs(rtts[i] - rtts[i - 1]);
            jitter = rtts.length > 1 ? jitter / (rtts.length - 1) : 0;
            return { ping, jitter };
        }

        // ---- Download ----
        async function downloadTest() {
            setPhase('download', 'dl');
            let totalBytes = 0;
            const start = performance.now();
            const deadline = start + CONFIG.graceMs + CONFIG.dlDuration * 1000;

            async function worker() {
                while (performance.now() < deadline && !aborted) {
                    try {
                        const resp = await fetch(
                            `/speedtest/download?bytes=${CONFIG.dlChunkBytes}&r=${Math.random()}`,
                            { cache: 'no-store', signal: newController().signal }
                        );
                        const reader = resp.body.getReader();
                        while (true) {
                            const { done, value } = await reader.read();
                            if (done) break;
                            totalBytes += value.length;
                            if (performance.now() >= deadline || aborted) {
                                reader.cancel();
                                break;
                            }
                        }
                    } catch (e) {
                        if (aborted) return;
                    }
                }
            }

            const meter = makeMeter('dl', () => totalBytes);

            const workers = [];
            for (let i = 0; i < CONFIG.dlStreams; i++) workers.push(worker());
            await Promise.all(workers);

            return meter.stop();
        }

        // ---- Upload ----
        function makeRandomBlob(size) {
            const buf = new Uint8Array(size);
            const CHUNK = 65536; // crypto.getRandomValues limit
            for (let off = 0; off < size; off += CHUNK) {
                crypto.getRandomValues(buf.subarray(off, Math.min(off + CHUNK, size)));
            }
            return new Blob([buf], { type: 'application/octet-stream' });
        }

        async function uploadTest() {
            setPhase('upload', 'ul');
            const blob = makeRandomBlob(CONFIG.ulChunkBytes);
            let totalBytes = 0;
            const start = performance.now();
            const deadline = start + CONFIG.graceMs + CONFIG.ulDuration * 1000;
            const activeXhrs = [];

            function worker() {
                return new Promise(resolve => {
                    function send() {
                        if (performance.now() >= deadline || aborted) return resolve();
                        const xhr = new XMLHttpRequest();
                        activeXhrs.push(xhr);
                        let lastLoaded = 0;
                        xhr.upload.onprogress = e => {
                            totalBytes += e.loaded - lastLoaded;
                            lastLoaded = e.loaded;
                        };
                        xhr.onload = send;
                        xhr.onerror = () => resolve();
                        xhr.onabort = () => resolve();
                        xhr.open('POST', '/speedtest/upload?r=' + Math.random());
                        xhr.setRequestHeader('Content-Type', 'application/octet-stream');
                        xhr.send(blob);
                    }
                    send();
                });
            }

            const meter = makeMeter('ul', () => totalBytes);

            // expose abort of xhrs
            uploadTest._abort = () => activeXhrs.forEach(x => { try { x.abort(); } catch (e) {} });

            const workers = [];
            for (let i = 0; i < CONFIG.ulStreams; i++) workers.push(worker());
            await Promise.all(workers);
            uploadTest._abort = null;

            return meter.stop();
        }

        // ---- Orchestration ----
        function fmt(v, decimals) {
            return v >= 100 ? v.toFixed(Math.max(0, decimals - 1)) : v.toFixed(decimals);
        }
        function setResult(id, value, unit) {
            const el = document.getElementById(id);
            el.classList.remove('pending');
            el.innerHTML = value + '<span class="u">' + unit + '</span>';
        }
        function resetResults() {
            [['res-dl', 'Mbps'], ['res-ul', 'Mbps'], ['res-ping', 'ms'], ['res-jitter', 'ms']].forEach(([id, u]) => {
                const el = document.getElementById(id);
                el.classList.add('pending');
                el.innerHTML = '&mdash;<span class="u">' + u + '</span>';
            });
        }

        async function startTest() {
            if (running) return;
            running = true;
            aborted = false;
            controllers = [];
            els.btnStart.disabled = true;
            els.btnAbort.disabled = false;
            els.targetBar.classList.add('testing');
            resetResults();
            setGauge(0, 'dl');

            try {
                const { ping, jitter } = await pingTest();
                if (!aborted) {
                    setResult('res-ping', fmt(ping, 1), 'ms');
                    setResult('res-jitter', fmt(jitter, 1), 'ms');
                }

                if (!aborted) {
                    const dl = await downloadTest();
                    if (!aborted) setResult('res-dl', fmt(dl, 2), 'Mbps');
                }

                if (!aborted) {
                    setGauge(0, 'ul');
                    const ul = await uploadTest();
                    if (!aborted) setResult('res-ul', fmt(ul, 2), 'Mbps');
                }
            } catch (e) {
                console.error(e);
            }

            finishTest();
        }

        function abortTest() {
            if (!running) return;
            aborted = true;
            controllers.forEach(c => { try { c.abort(); } catch (e) {} });
            if (uploadTest._abort) uploadTest._abort();
        }

        function finishTest() {
            running = false;
            els.btnStart.disabled = false;
            els.btnAbort.disabled = true;
            els.targetBar.classList.remove('testing');
            setPhase(aborted ? 'aborted' : 'done', null);
            if (aborted) setGauge(0, 'dl');
        }

        // Init
        document.getElementById('target-host').textContent = window.location.host;
        setGauge(0, 'dl');
    </script>
@endsection
