@extends('layouts.app')

@section('title', 'Regex Tester')

@section('styles')
    .main {
        flex: 1;
        display: grid;
        grid-template-columns: 340px 1fr;
        gap: 0;
    }

    /* Left panel */
    .left-panel {
        border-right: 1px solid var(--border);
        display: flex;
        flex-direction: column;
        min-height: 0;
    }

    .pane-header {
        padding: 0.75rem 1.25rem;
        border-bottom: 1px solid var(--border);
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: var(--bg-panel);
    }

    .pane-title {
        font-size: 0.7rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        color: var(--text-dim);
    }

    .pane-title.active {
        color: var(--accent);
    }

    .pane-meta {
        font-size: 0.65rem;
        color: var(--text-dim);
    }

    /* Pattern input */
    .pattern-section {
        padding: 1rem 1.25rem;
        background: var(--bg-panel);
        border-bottom: 1px solid var(--border);
    }

    .pattern-input-wrap {
        display: flex;
        align-items: center;
        gap: 0;
        background: var(--bg-elevated);
        border: 1px solid var(--border);
        font-family: 'JetBrains Mono', monospace;
        font-size: 0.82rem;
        transition: border-color 0.15s ease;
    }

    .pattern-input-wrap:focus-within {
        border-color: var(--accent);
    }

    .pattern-delim {
        color: var(--accent);
        padding: 0.5rem 0.35rem;
        font-weight: 700;
        user-select: none;
        flex-shrink: 0;
    }

    .pattern-delim:first-child {
        padding-left: 0.75rem;
    }

    #pattern-input {
        flex: 1;
        background: transparent;
        border: none;
        color: var(--text-primary);
        font-family: 'JetBrains Mono', monospace;
        font-size: 0.82rem;
        padding: 0.5rem 0;
        outline: none;
        min-width: 0;
    }

    #pattern-input::placeholder {
        color: var(--text-dim);
    }

    .flag-toggles {
        display: flex;
        align-items: center;
        gap: 0.25rem;
        margin-top: 0.75rem;
    }

    .flag-toggles-label {
        font-size: 0.65rem;
        color: var(--text-dim);
        margin-right: 0.25rem;
    }

    .flag-btn {
        font-family: 'JetBrains Mono', monospace;
        font-size: 0.65rem;
        padding: 0.15rem 0.45rem;
        background: transparent;
        border: 1px solid var(--border);
        color: var(--text-dim);
        cursor: pointer;
        transition: all 0.15s ease;
    }

    .flag-btn:hover {
        border-color: var(--border-focus);
        color: var(--text-secondary);
    }

    .flag-btn.active {
        background: var(--accent);
        border-color: var(--accent);
        color: var(--bg-deep);
        font-weight: 600;
    }

    /* Error banner */
    .error-banner {
        padding: 0.6rem 1.25rem;
        background: var(--error-dim);
        border-bottom: 1px solid rgba(255, 77, 106, 0.15);
        font-size: 0.72rem;
        color: var(--error);
        display: none;
    }

    .error-banner.show {
        display: block;
    }

    /* Match results */
    .matches-section {
        flex: 1;
        overflow-y: auto;
        background: var(--bg-panel);
    }

    .matches-empty {
        padding: 2rem 1.25rem;
        text-align: center;
        color: var(--text-dim);
        font-size: 0.75rem;
    }

    .match-card {
        padding: 0.75rem 1.25rem;
        border-bottom: 1px solid var(--border);
        animation: fadeSlideIn 0.2s ease both;
    }

    .match-card:hover {
        background: rgba(87, 64, 153, 0.02);
    }

    @keyframes fadeSlideIn {
        from { opacity: 0; transform: translateY(2px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .match-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 0.35rem;
    }

    .match-index {
        font-size: 0.65rem;
        font-weight: 600;
        color: var(--accent);
    }

    .match-pos {
        font-size: 0.62rem;
        color: var(--text-dim);
    }

    .match-text {
        font-family: 'JetBrains Mono', monospace;
        font-size: 0.75rem;
        padding: 0.3rem 0.5rem;
        background: rgba(87, 64, 153, 0.08);
        border: 1px solid rgba(87, 64, 153, 0.15);
        color: var(--text-primary);
        word-break: break-all;
    }

    .match-groups {
        margin-top: 0.4rem;
    }

    .match-group {
        font-size: 0.68rem;
        color: var(--text-secondary);
        padding: 0.15rem 0;
    }

    .match-group-label {
        color: var(--text-dim);
        font-weight: 500;
    }

    /* Right panel - test string */
    .right-panel {
        display: flex;
        flex-direction: column;
        min-height: 0;
    }

    .test-string-body {
        flex: 1;
        position: relative;
        background: var(--bg-panel);
        min-height: calc(100vh - 175px);
    }

    #test-input {
        width: 100%;
        height: 100%;
        min-height: calc(100vh - 175px);
        position: absolute;
        top: 0;
        left: 0;
        z-index: 2;
        background: transparent;
        border: none;
        color: transparent;
        caret-color: var(--text-primary);
        font-family: 'JetBrains Mono', monospace;
        font-size: 0.82rem;
        line-height: 1.7;
        padding: 1.25rem;
        resize: none;
        outline: none;
        white-space: pre-wrap;
        word-break: break-word;
        overflow: auto;
    }

    #test-input::placeholder {
        color: var(--text-dim);
    }

    #test-display {
        width: 100%;
        height: 100%;
        min-height: calc(100vh - 175px);
        position: absolute;
        top: 0;
        left: 0;
        z-index: 1;
        font-family: 'JetBrains Mono', monospace;
        font-size: 0.82rem;
        line-height: 1.7;
        padding: 1.25rem;
        white-space: pre-wrap;
        word-break: break-word;
        color: var(--text-primary);
        overflow: auto;
        pointer-events: none;
    }

    #test-display mark {
        background: rgba(87, 64, 153, 0.3);
        color: var(--text-primary);
        border-bottom: 2px solid var(--accent);
        border-radius: 0;
    }

    /* Quick reference */
    .quick-ref {
        border-top: 1px solid var(--border);
        background: var(--bg-panel);
    }

    .section-toggle {
        font-family: 'JetBrains Mono', monospace;
        font-size: 0.65rem;
        font-weight: 500;
        color: var(--text-dim);
        background: none;
        border: none;
        cursor: pointer;
        padding: 0.6rem 1.25rem;
        display: flex;
        align-items: center;
        gap: 0.4rem;
        transition: color 0.15s ease;
        width: 100%;
        text-align: left;
    }

    .section-toggle:hover {
        color: var(--text-secondary);
    }

    .section-toggle .arrow {
        display: inline-block;
        transition: transform 0.15s ease;
        font-size: 0.55rem;
    }

    .section-toggle.open .arrow {
        transform: rotate(90deg);
    }

    .section-content {
        display: none;
        padding: 0 1.25rem 1rem;
    }

    .section-content.show {
        display: block;
    }

    .ref-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 0.35rem;
    }

    .ref-item {
        display: flex;
        gap: 0.5rem;
        font-size: 0.65rem;
        padding: 0.2rem 0;
    }

    .ref-token {
        font-family: 'JetBrains Mono', monospace;
        color: var(--accent);
        font-weight: 600;
        min-width: 3.5rem;
    }

    .ref-desc {
        color: var(--text-dim);
    }

    /* Status bar */
    .statusbar {
        border-top: 1px solid var(--border);
        padding: 0.4rem 1.25rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        font-size: 0.65rem;
        color: var(--text-dim);
        background: var(--bg-deep);
    }

    .statusbar-left,
    .statusbar-right {
        display: flex;
        align-items: center;
        gap: 1.25rem;
    }

    .status-dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: var(--text-dim);
        display: inline-block;
        margin-right: 0.35rem;
    }

    .status-dot.valid {
        background: var(--accent);
        box-shadow: 0 0 6px rgba(87, 64, 153, 0.4);
    }

    .status-dot.invalid {
        background: var(--error);
        box-shadow: 0 0 6px rgba(255, 77, 106, 0.4);
    }

    .copy-feedback {
        position: fixed;
        bottom: 2rem;
        left: 50%;
        transform: translateX(-50%) translateY(10px);
        background: var(--accent);
        color: var(--bg-deep);
        padding: 0.5rem 1.25rem;
        font-size: 0.72rem;
        font-weight: 600;
        z-index: 100;
        opacity: 0;
        transition: all 0.25s ease;
        pointer-events: none;
    }

    .copy-feedback.show {
        opacity: 1;
        transform: translateX(-50%) translateY(0);
    }

    @media (max-width: 900px) {
        .main {
            grid-template-columns: 1fr;
        }
        .left-panel {
            border-right: none;
            border-bottom: 1px solid var(--border);
            max-height: 50vh;
        }
        .test-string-body,
        #test-input,
        #test-display {
            min-height: 40vh;
        }
    }
@endsection

@section('actions')
    <button class="btn" onclick="loadSample()">sample</button>
    <button class="btn btn-danger" onclick="clearAll()">clear</button>
@endsection

@section('content')
    <main class="main">
        <div class="left-panel">
            <div class="pane-header">
                <div class="pane-title active">pattern</div>
            </div>

            <div class="pattern-section">
                <div class="pattern-input-wrap">
                    <span class="pattern-delim">/</span>
                    <input type="text" id="pattern-input" placeholder="\d+" spellcheck="false" autocomplete="off">
                    <span class="pattern-delim">/</span>
                </div>
                <div class="flag-toggles">
                    <span class="flag-toggles-label">flags:</span>
                    <button class="flag-btn active" data-flag="g" onclick="toggleFlag(this)">g</button>
                    <button class="flag-btn" data-flag="i" onclick="toggleFlag(this)">i</button>
                    <button class="flag-btn" data-flag="m" onclick="toggleFlag(this)">m</button>
                </div>
            </div>

            <div class="error-banner" id="error-banner"></div>

            <div class="pane-header">
                <div class="pane-title" id="matches-title">matches</div>
                <div class="pane-meta" id="match-count"></div>
            </div>

            <div class="matches-section" id="matches-section">
                <div class="matches-empty">No matches</div>
            </div>

            <div class="quick-ref">
                <button class="section-toggle" onclick="toggleSection(this, 'ref-content')">
                    <span class="arrow">&#9654;</span> quick reference
                </button>
                <div class="section-content" id="ref-content">
                    <div class="ref-grid">
                        <div class="ref-item"><span class="ref-token">.</span><span class="ref-desc">any char</span></div>
                        <div class="ref-item"><span class="ref-token">\d</span><span class="ref-desc">digit</span></div>
                        <div class="ref-item"><span class="ref-token">\w</span><span class="ref-desc">word char</span></div>
                        <div class="ref-item"><span class="ref-token">\s</span><span class="ref-desc">whitespace</span></div>
                        <div class="ref-item"><span class="ref-token">^</span><span class="ref-desc">start</span></div>
                        <div class="ref-item"><span class="ref-token">$</span><span class="ref-desc">end</span></div>
                        <div class="ref-item"><span class="ref-token">*</span><span class="ref-desc">0 or more</span></div>
                        <div class="ref-item"><span class="ref-token">+</span><span class="ref-desc">1 or more</span></div>
                        <div class="ref-item"><span class="ref-token">?</span><span class="ref-desc">0 or 1</span></div>
                        <div class="ref-item"><span class="ref-token">{n,m}</span><span class="ref-desc">n to m</span></div>
                        <div class="ref-item"><span class="ref-token">[abc]</span><span class="ref-desc">char class</span></div>
                        <div class="ref-item"><span class="ref-token">(abc)</span><span class="ref-desc">group</span></div>
                        <div class="ref-item"><span class="ref-token">a|b</span><span class="ref-desc">alternation</span></div>
                        <div class="ref-item"><span class="ref-token">\b</span><span class="ref-desc">word boundary</span></div>
                        <div class="ref-item"><span class="ref-token">(?:)</span><span class="ref-desc">non-capture</span></div>
                        <div class="ref-item"><span class="ref-token">(?=)</span><span class="ref-desc">lookahead</span></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="right-panel">
            <div class="pane-header">
                <div class="pane-title" id="test-title">test string</div>
                <div class="pane-meta" id="test-meta"></div>
            </div>
            <div class="test-string-body">
                <textarea
                    id="test-input"
                    placeholder="Type or paste your test string here..."
                    spellcheck="false"
                    autocomplete="off"
                    autocorrect="off"
                    autocapitalize="off"
                ></textarea>
                <div id="test-display"></div>
            </div>
        </div>
    </main>

    <div class="statusbar">
        <div class="statusbar-left">
            <span><span class="status-dot" id="status-dot"></span><span id="status-text">ready</span></span>
            <span id="pattern-summary"></span>
        </div>
        <div class="statusbar-right">
            <span id="char-count"></span>
        </div>
    </div>

    <div class="copy-feedback" id="copy-feedback">Copied to clipboard</div>
@endsection

@section('scripts')
    <script>
        const patternInput = document.getElementById('pattern-input');
        const testInput = document.getElementById('test-input');
        const testDisplay = document.getElementById('test-display');
        const matchesSection = document.getElementById('matches-section');
        const errorBanner = document.getElementById('error-banner');
        const matchCount = document.getElementById('match-count');
        const matchesTitle = document.getElementById('matches-title');
        const testTitle = document.getElementById('test-title');
        const statusDot = document.getElementById('status-dot');
        const statusText = document.getElementById('status-text');
        const patternSummary = document.getElementById('pattern-summary');
        const charCount = document.getElementById('char-count');

        let flags = { g: true, i: false, m: false };

        patternInput.addEventListener('input', runMatch);
        testInput.addEventListener('input', () => {
            runMatch();
            syncScroll();
        });
        testInput.addEventListener('scroll', syncScroll);

        function syncScroll() {
            testDisplay.scrollTop = testInput.scrollTop;
            testDisplay.scrollLeft = testInput.scrollLeft;
        }

        function getFlags() {
            return Object.entries(flags).filter(([_, v]) => v).map(([k]) => k).join('');
        }

        function toggleFlag(btn) {
            const flag = btn.dataset.flag;
            flags[flag] = !flags[flag];
            btn.classList.toggle('active', flags[flag]);
            runMatch();
        }

        function toggleSection(btn, targetId) {
            btn.classList.toggle('open');
            document.getElementById(targetId).classList.toggle('show');
        }

        function runMatch() {
            const pattern = patternInput.value;
            const testStr = testInput.value;

            charCount.textContent = testStr.length ? testStr.length + ' chars' : '';

            if (!pattern) {
                testDisplay.textContent = testStr;
                matchesSection.innerHTML = '<div class="matches-empty">Enter a pattern</div>';
                matchCount.textContent = '';
                matchesTitle.classList.remove('active');
                testTitle.classList.remove('active');
                errorBanner.classList.remove('show');
                statusDot.className = 'status-dot';
                statusText.textContent = 'ready';
                patternSummary.textContent = '';
                return;
            }

            let regex;
            try {
                regex = new RegExp(pattern, getFlags());
                errorBanner.classList.remove('show');
            } catch (e) {
                errorBanner.textContent = e.message;
                errorBanner.classList.add('show');
                testDisplay.textContent = testStr;
                matchesSection.innerHTML = '<div class="matches-empty">Fix pattern error</div>';
                matchCount.textContent = '';
                matchesTitle.classList.remove('active');
                statusDot.className = 'status-dot invalid';
                statusText.textContent = 'invalid pattern';
                patternSummary.textContent = '';
                return;
            }

            if (!testStr) {
                testDisplay.textContent = '';
                matchesSection.innerHTML = '<div class="matches-empty">Enter test string</div>';
                matchCount.textContent = '';
                matchesTitle.classList.remove('active');
                testTitle.classList.remove('active');
                statusDot.className = 'status-dot valid';
                statusText.textContent = 'valid pattern';
                patternSummary.textContent = '/' + pattern + '/' + getFlags();
                return;
            }

            // Find all matches
            const matches = [];
            if (flags.g) {
                let m;
                const safeRegex = new RegExp(pattern, getFlags());
                while ((m = safeRegex.exec(testStr)) !== null) {
                    matches.push(m);
                    if (m[0].length === 0) {
                        safeRegex.lastIndex++;
                    }
                }
            } else {
                const m = regex.exec(testStr);
                if (m) matches.push(m);
            }

            // Highlight in test display
            if (matches.length > 0) {
                let html = '';
                let lastIndex = 0;
                for (const m of matches) {
                    html += escapeHtml(testStr.slice(lastIndex, m.index));
                    html += '<mark>' + escapeHtml(m[0]) + '</mark>';
                    lastIndex = m.index + m[0].length;
                }
                html += escapeHtml(testStr.slice(lastIndex));
                testDisplay.innerHTML = html;

                testTitle.classList.add('active');
                matchesTitle.classList.add('active');
                statusDot.className = 'status-dot valid';
                statusText.textContent = matches.length + ' match' + (matches.length !== 1 ? 'es' : '');
            } else {
                testDisplay.textContent = testStr;
                testTitle.classList.remove('active');
                matchesTitle.classList.remove('active');
                statusDot.className = 'status-dot valid';
                statusText.textContent = 'no matches';
            }

            matchCount.textContent = matches.length + ' match' + (matches.length !== 1 ? 'es' : '');
            patternSummary.textContent = '/' + pattern + '/' + getFlags();

            // Render match cards
            if (matches.length === 0) {
                matchesSection.innerHTML = '<div class="matches-empty">No matches</div>';
            } else {
                let cardsHtml = '';
                matches.forEach((m, i) => {
                    const delay = Math.min(i * 30, 300);
                    cardsHtml += '<div class="match-card" style="animation-delay:' + delay + 'ms">';
                    cardsHtml += '<div class="match-header"><span class="match-index">Match ' + (i + 1) + '</span><span class="match-pos">' + m.index + '-' + (m.index + m[0].length) + '</span></div>';
                    cardsHtml += '<div class="match-text">' + escapeHtml(m[0]) + '</div>';

                    if (m.length > 1) {
                        cardsHtml += '<div class="match-groups">';
                        for (let g = 1; g < m.length; g++) {
                            cardsHtml += '<div class="match-group"><span class="match-group-label">$' + g + '</span> ' + escapeHtml(m[g] !== undefined ? m[g] : '') + '</div>';
                        }
                        cardsHtml += '</div>';
                    }

                    cardsHtml += '</div>';
                });
                matchesSection.innerHTML = cardsHtml;
            }
        }

        function loadSample() {
            patternInput.value = '(\\w+)@(\\w+\\.\\w+)';
            testInput.value = 'Contact us at hello@example.com or support@company.org for help.\nYou can also reach admin@test.io.';
            flags = { g: true, i: false, m: false };
            document.querySelectorAll('.flag-btn').forEach(btn => {
                btn.classList.toggle('active', flags[btn.dataset.flag]);
            });
            runMatch();
        }

        function clearAll() {
            patternInput.value = '';
            testInput.value = '';
            testDisplay.textContent = '';
            matchesSection.innerHTML = '<div class="matches-empty">No matches</div>';
            matchCount.textContent = '';
            matchesTitle.classList.remove('active');
            testTitle.classList.remove('active');
            errorBanner.classList.remove('show');
            statusDot.className = 'status-dot';
            statusText.textContent = 'ready';
            patternSummary.textContent = '';
            charCount.textContent = '';
        }

        function escapeHtml(str) {
            const div = document.createElement('div');
            div.textContent = str;
            return div.innerHTML;
        }
    </script>
@endsection
