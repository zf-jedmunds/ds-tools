@extends('layouts.app')

@section('title', 'JWT Debugger')

@section('styles')
    :root {
        --syn-key: #9b7fdb;
        --syn-string: #7dd3fc;
        --syn-number: #f0abfc;
        --syn-bool: #fbbf24;
        --syn-null: #6b7280;
        --syn-bracket: #636380;
        --syn-colon: #636380;
    }

    .main {
        flex: 1;
        display: flex;
        flex-direction: column;
    }

    /* JWT input strip */
    .jwt-input-strip {
        border-bottom: 1px solid var(--border);
        background: var(--bg-panel);
    }

    .jwt-input-strip .pane-header {
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

    #jwt-input {
        width: 100%;
        height: 80px;
        background: transparent;
        border: none;
        color: var(--text-primary);
        font-family: 'JetBrains Mono', monospace;
        font-size: 0.78rem;
        line-height: 1.5;
        padding: 0.75rem 1.25rem;
        resize: none;
        outline: none;
    }

    #jwt-input::placeholder {
        color: var(--text-dim);
    }

    #jwt-input:focus {
        background: rgba(87, 64, 153, 0.01);
    }

    /* Decoded panes */
    .decoded-panes {
        flex: 1;
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 0;
    }

    .pane {
        display: flex;
        flex-direction: column;
        min-height: 0;
    }

    .pane:first-child {
        border-right: 1px solid var(--border);
    }

    .pane-header {
        padding: 0.75rem 1.25rem;
        border-bottom: 1px solid var(--border);
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: var(--bg-panel);
    }

    .pane-header-left {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .pane-body {
        flex: 1;
        position: relative;
        background: var(--bg-panel);
        overflow: auto;
    }

    .json-display {
        padding: 1.25rem;
        min-height: 200px;
    }

    .output-placeholder {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        height: 100%;
        min-height: 300px;
        gap: 1rem;
        color: var(--text-dim);
    }

    .output-placeholder svg {
        opacity: 0.15;
    }

    .output-placeholder p {
        font-size: 0.78rem;
        text-align: center;
        line-height: 1.8;
    }

    /* JSON lines */
    .json-line {
        display: block;
        font-size: 0.82rem;
        line-height: 1.7;
        white-space: pre;
        animation: fadeSlideIn 0.3s ease both;
    }

    .json-line:hover {
        background: rgba(87, 64, 153, 0.02);
    }

    .line-number {
        display: inline-block;
        width: 2.5em;
        text-align: right;
        padding-right: 1em;
        color: var(--text-dim);
        user-select: none;
        opacity: 0.5;
        font-size: 0.72rem;
    }

    @keyframes fadeSlideIn {
        from { opacity: 0; transform: translateY(2px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .syn-key { color: var(--syn-key); }
    .syn-string { color: var(--syn-string); }
    .syn-number { color: var(--syn-number); }
    .syn-bool { color: var(--syn-bool); }
    .syn-null { color: var(--syn-null); font-style: italic; }
    .syn-bracket { color: var(--syn-bracket); }
    .syn-colon { color: var(--syn-colon); }
    .syn-comma { color: var(--syn-colon); }

    /* Algorithm badge */
    .alg-badge {
        font-family: 'JetBrains Mono', monospace;
        font-size: 0.6rem;
        font-weight: 700;
        padding: 0.2rem 0.5rem;
        letter-spacing: 0.05em;
        background: rgba(87, 64, 153, 0.12);
        color: var(--accent);
        border: 1px solid rgba(87, 64, 153, 0.25);
    }

    /* Claims summary */
    .claims-summary {
        padding: 1rem 1.25rem;
        border-top: 1px solid var(--border);
        background: var(--bg-elevated);
    }

    .claims-label {
        font-size: 0.65rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        color: var(--text-dim);
        margin-bottom: 0.75rem;
    }

    .claim-row {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        font-size: 0.72rem;
        padding: 0.25rem 0;
    }

    .claim-key {
        color: var(--syn-key);
        font-weight: 500;
        min-width: 3rem;
    }

    .claim-value {
        color: var(--text-secondary);
    }

    /* Expiry badge */
    .exp-badge {
        font-family: 'JetBrains Mono', monospace;
        font-size: 0.6rem;
        font-weight: 600;
        padding: 0.15rem 0.5rem;
    }

    .exp-badge.expired {
        background: rgba(255, 77, 106, 0.12);
        color: var(--error);
        border: 1px solid rgba(255, 77, 106, 0.25);
    }

    .exp-badge.valid {
        background: rgba(87, 64, 153, 0.12);
        color: var(--accent);
        border: 1px solid rgba(87, 64, 153, 0.25);
    }

    .exp-badge.none {
        background: var(--bg-panel);
        color: var(--text-dim);
        border: 1px solid var(--border);
    }

    /* Disclaimer */
    .disclaimer {
        font-size: 0.65rem;
        color: var(--text-dim);
        padding: 0.5rem 1.25rem;
        border-top: 1px solid var(--border);
        background: var(--bg-panel);
        font-style: italic;
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
        .decoded-panes {
            grid-template-columns: 1fr;
        }
        .pane:first-child {
            border-right: none;
            border-bottom: 1px solid var(--border);
        }
    }
@endsection

@section('actions')
    <button class="btn" onclick="copyHeader()">copy header</button>
    <button class="btn" onclick="copyPayload()">copy payload</button>
    <button class="btn btn-danger" onclick="clearAll()">clear</button>
@endsection

@section('content')
    <main class="main">
        <div class="jwt-input-strip">
            <div class="pane-header">
                <div class="pane-title active">jwt token</div>
                <div class="pane-meta" id="input-meta"></div>
            </div>
            <textarea
                id="jwt-input"
                placeholder="Paste a JWT token here... (e.g. eyJhbGciOiJIUzI1NiIs...)"
                spellcheck="false"
                autocomplete="off"
                autocorrect="off"
                autocapitalize="off"
            ></textarea>
        </div>

        <div class="decoded-panes">
            <div class="pane">
                <div class="pane-header">
                    <div class="pane-header-left">
                        <div class="pane-title" id="header-title">header</div>
                        <span class="alg-badge" id="alg-badge" style="display:none"></span>
                    </div>
                    <div class="pane-meta" id="header-meta"></div>
                </div>
                <div class="pane-body">
                    <div class="json-display" id="header-output">
                        <div class="output-placeholder">
                            <p>Header will appear here</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="pane">
                <div class="pane-header">
                    <div class="pane-title" id="payload-title">payload</div>
                    <div class="pane-meta" id="payload-meta"></div>
                </div>
                <div class="pane-body">
                    <div class="json-display" id="payload-output">
                        <div class="output-placeholder">
                            <p>Payload will appear here</p>
                        </div>
                    </div>
                    <div class="claims-summary" id="claims-summary" style="display:none"></div>
                </div>
                <div class="disclaimer" id="disclaimer" style="display:none">Signature not verified &mdash; decode only</div>
            </div>
        </div>
    </main>

    <div class="statusbar">
        <div class="statusbar-left">
            <span><span class="status-dot" id="status-dot"></span><span id="status-text">ready</span></span>
            <span id="alg-info"></span>
        </div>
        <div class="statusbar-right">
            <span id="parts-info"></span>
        </div>
    </div>

    <div class="copy-feedback" id="copy-feedback">Copied to clipboard</div>
@endsection

@section('scripts')
    <script>
        let decodedHeader = null;
        let decodedPayload = null;

        const input = document.getElementById('jwt-input');
        const headerOutput = document.getElementById('header-output');
        const payloadOutput = document.getElementById('payload-output');
        const claimsSummary = document.getElementById('claims-summary');
        const disclaimer = document.getElementById('disclaimer');
        const algBadge = document.getElementById('alg-badge');
        const statusDot = document.getElementById('status-dot');
        const statusText = document.getElementById('status-text');
        const algInfo = document.getElementById('alg-info');
        const partsInfo = document.getElementById('parts-info');
        const inputMeta = document.getElementById('input-meta');
        const headerTitle = document.getElementById('header-title');
        const payloadTitle = document.getElementById('payload-title');

        input.addEventListener('input', decode);
        input.addEventListener('paste', () => setTimeout(decode, 10));

        function base64UrlDecode(str) {
            let b64 = str.replace(/-/g, '+').replace(/_/g, '/');
            while (b64.length % 4) b64 += '=';
            return decodeURIComponent(escape(atob(b64)));
        }

        function decode() {
            const token = input.value.trim();
            if (!token) {
                resetOutput();
                return;
            }

            const parts = token.split('.');
            if (parts.length !== 3) {
                showError('JWT must have 3 parts (header.payload.signature)');
                return;
            }

            try {
                const headerJson = base64UrlDecode(parts[0]);
                decodedHeader = JSON.parse(headerJson);
            } catch (e) {
                showError('Failed to decode header');
                return;
            }

            try {
                const payloadJson = base64UrlDecode(parts[1]);
                decodedPayload = JSON.parse(payloadJson);
            } catch (e) {
                showError('Failed to decode payload');
                return;
            }

            // Render header
            const headerFormatted = JSON.stringify(decodedHeader, null, 2);
            renderHighlighted(headerOutput, headerFormatted);
            headerTitle.classList.add('active');

            // Algorithm badge
            if (decodedHeader.alg) {
                algBadge.textContent = decodedHeader.alg;
                algBadge.style.display = '';
                algInfo.textContent = decodedHeader.alg;
            }

            // Render payload
            const payloadFormatted = JSON.stringify(decodedPayload, null, 2);
            renderHighlighted(payloadOutput, payloadFormatted);
            payloadTitle.classList.add('active');

            // Claims summary
            renderClaims(decodedPayload);

            // Status
            statusDot.className = 'status-dot valid';
            statusText.textContent = 'decoded';
            disclaimer.style.display = '';

            const headerBytes = new Blob([parts[0]]).size;
            const payloadBytes = new Blob([parts[1]]).size;
            const sigBytes = new Blob([parts[2]]).size;
            partsInfo.textContent = 'header ' + headerBytes + 'B \u00b7 payload ' + payloadBytes + 'B \u00b7 sig ' + sigBytes + 'B';
            inputMeta.textContent = token.length + ' chars';
        }

        function renderHighlighted(container, json) {
            const lines = json.split('\n');
            let html = '';
            lines.forEach((line, i) => {
                const delay = Math.min(i * 8, 400);
                const highlighted = syntaxHighlight(escapeHtml(line));
                html += '<span class="json-line" style="animation-delay:' + delay + 'ms"><span class="line-number">' + (i + 1) + '</span>' + highlighted + '</span>';
            });
            container.innerHTML = html;
        }

        function renderClaims(payload) {
            let html = '<div class="claims-label">Claims</div>';

            if (payload.iat !== undefined) {
                const date = new Date(payload.iat * 1000);
                html += '<div class="claim-row"><span class="claim-key">iat</span><span class="claim-value">' + escapeHtml(date.toLocaleString()) + '</span></div>';
            }

            if (payload.exp !== undefined) {
                const date = new Date(payload.exp * 1000);
                const now = Date.now();
                const isExpired = payload.exp * 1000 < now;
                const badgeClass = isExpired ? 'expired' : 'valid';
                const badgeText = isExpired ? 'expired' : 'valid';
                html += '<div class="claim-row"><span class="claim-key">exp</span><span class="claim-value">' + escapeHtml(date.toLocaleString()) + '</span> <span class="exp-badge ' + badgeClass + '">' + badgeText + '</span></div>';
            } else {
                html += '<div class="claim-row"><span class="claim-key">exp</span><span class="exp-badge none">no exp claim</span></div>';
            }

            if (payload.sub !== undefined) {
                html += '<div class="claim-row"><span class="claim-key">sub</span><span class="claim-value">' + escapeHtml(String(payload.sub)) + '</span></div>';
            }
            if (payload.iss !== undefined) {
                html += '<div class="claim-row"><span class="claim-key">iss</span><span class="claim-value">' + escapeHtml(String(payload.iss)) + '</span></div>';
            }
            if (payload.aud !== undefined) {
                const aud = Array.isArray(payload.aud) ? payload.aud.join(', ') : String(payload.aud);
                html += '<div class="claim-row"><span class="claim-key">aud</span><span class="claim-value">' + escapeHtml(aud) + '</span></div>';
            }

            claimsSummary.innerHTML = html;
            claimsSummary.style.display = '';
        }

        function showError(msg) {
            decodedHeader = null;
            decodedPayload = null;
            headerOutput.innerHTML = '<div class="output-placeholder"><p style="color:var(--error)">' + escapeHtml(msg) + '</p></div>';
            payloadOutput.innerHTML = '<div class="output-placeholder"><p style="color:var(--error)">' + escapeHtml(msg) + '</p></div>';
            headerTitle.classList.remove('active');
            payloadTitle.classList.remove('active');
            algBadge.style.display = 'none';
            claimsSummary.style.display = 'none';
            disclaimer.style.display = 'none';
            statusDot.className = 'status-dot invalid';
            statusText.textContent = 'invalid token';
            algInfo.textContent = '';
            partsInfo.textContent = '';
        }

        function resetOutput() {
            decodedHeader = null;
            decodedPayload = null;
            headerOutput.innerHTML = '<div class="output-placeholder"><p>Header will appear here</p></div>';
            payloadOutput.innerHTML = '<div class="output-placeholder"><p>Payload will appear here</p></div>';
            headerTitle.classList.remove('active');
            payloadTitle.classList.remove('active');
            algBadge.style.display = 'none';
            claimsSummary.style.display = 'none';
            disclaimer.style.display = 'none';
            statusDot.className = 'status-dot';
            statusText.textContent = 'ready';
            algInfo.textContent = '';
            partsInfo.textContent = '';
            inputMeta.textContent = '';
        }

        function copyHeader() {
            if (!decodedHeader) return;
            copyText(JSON.stringify(decodedHeader, null, 2));
        }

        function copyPayload() {
            if (!decodedPayload) return;
            copyText(JSON.stringify(decodedPayload, null, 2));
        }

        function copyText(text) {
            navigator.clipboard.writeText(text).then(() => {
                const feedback = document.getElementById('copy-feedback');
                feedback.classList.add('show');
                setTimeout(() => feedback.classList.remove('show'), 1500);
            });
        }

        function clearAll() {
            input.value = '';
            resetOutput();
        }

        function syntaxHighlight(str) {
            return str
                .replace(/&quot;([^&]*?)&quot;\s*:/g, '<span class="syn-key">&quot;$1&quot;</span><span class="syn-colon">:</span>')
                .replace(/&quot;([^&]*?)&quot;/g, '<span class="syn-string">&quot;$1&quot;</span>')
                .replace(/\b(-?\d+\.?\d*(?:[eE][+-]?\d+)?)\b/g, '<span class="syn-number">$1</span>')
                .replace(/\b(true|false)\b/g, '<span class="syn-bool">$1</span>')
                .replace(/\bnull\b/g, '<span class="syn-null">null</span>')
                .replace(/([[\]{}])/g, '<span class="syn-bracket">$1</span>')
                .replace(/,/g, '<span class="syn-comma">,</span>');
        }

        function escapeHtml(str) {
            const div = document.createElement('div');
            div.textContent = str;
            return div.innerHTML;
        }
    </script>
@endsection
