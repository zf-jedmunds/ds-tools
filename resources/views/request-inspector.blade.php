@extends('layouts.app')

@section('title', 'Request Inspector')

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
        display: grid;
        grid-template-columns: 340px 1fr;
        gap: 0;
    }

    /* Left info panel */
    .info-panel {
        border-right: 1px solid var(--border);
        display: flex;
        flex-direction: column;
    }

    .info-panel .pane-header {
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

    .info-body {
        padding: 1.5rem 1.25rem;
        background: var(--bg-panel);
        flex: 1;
    }

    .info-section {
        margin-bottom: 2rem;
    }

    .info-label {
        font-size: 0.65rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        color: var(--text-dim);
        margin-bottom: 0.75rem;
    }

    .webhook-url-box {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        background: var(--bg-elevated);
        border: 1px solid var(--border);
        padding: 0.6rem 0.75rem;
    }

    .webhook-url-box code {
        flex: 1;
        font-size: 0.72rem;
        color: var(--accent);
        word-break: break-all;
        line-height: 1.5;
    }

    .copy-btn {
        font-family: 'JetBrains Mono', monospace;
        font-size: 0.65rem;
        padding: 0.3rem 0.6rem;
        background: transparent;
        border: 1px solid var(--border);
        color: var(--text-secondary);
        cursor: pointer;
        transition: all 0.15s ease;
        flex-shrink: 0;
    }

    .copy-btn:hover {
        border-color: var(--border-focus);
        color: var(--text-primary);
    }

    .copy-btn.copied {
        color: var(--accent);
        border-color: var(--accent);
    }

    .info-hint {
        font-size: 0.68rem;
        color: var(--text-dim);
        line-height: 1.7;
        margin-top: 1rem;
    }

    .info-hint code {
        font-size: 0.65rem;
        background: var(--bg-elevated);
        padding: 0.15rem 0.4rem;
        border: 1px solid var(--border);
        color: var(--text-secondary);
    }

    .method-list {
        display: flex;
        gap: 0.35rem;
        margin-top: 0.75rem;
    }

    .method-chip {
        font-size: 0.6rem;
        font-weight: 600;
        padding: 0.2rem 0.5rem;
        border: 1px solid var(--border);
        color: var(--text-secondary);
    }

    /* Right feed panel */
    .feed-panel {
        display: flex;
        flex-direction: column;
        min-height: 0;
    }

    .feed-panel .pane-header {
        padding: 0.75rem 1.25rem;
        border-bottom: 1px solid var(--border);
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: var(--bg-panel);
    }

    .pane-meta {
        font-size: 0.65rem;
        color: var(--text-dim);
    }

    .feed-body {
        flex: 1;
        overflow-y: auto;
        background: var(--bg-panel);
        min-height: calc(100vh - 175px);
    }

    /* Empty state */
    .feed-empty {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        height: 100%;
        min-height: calc(100vh - 250px);
        gap: 1rem;
        color: var(--text-dim);
    }

    .feed-empty svg {
        opacity: 0.15;
    }

    .feed-empty p {
        font-size: 0.78rem;
        text-align: center;
        line-height: 1.8;
    }

    .pulse-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: var(--accent);
        display: inline-block;
        animation: pulse 2s ease-in-out infinite;
    }

    @keyframes pulse {
        0%, 100% { opacity: 0.3; }
        50% { opacity: 1; box-shadow: 0 0 8px rgba(87, 64, 153, 0.5); }
    }

    /* Request card */
    .request-card {
        border-bottom: 1px solid var(--border);
        padding: 1rem 1.25rem;
        animation: fadeSlideIn 0.3s ease both;
    }

    .request-card:hover {
        background: rgba(87, 64, 153, 0.01);
    }

    @keyframes fadeSlideIn {
        from { opacity: 0; transform: translateY(-4px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .request-header {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        margin-bottom: 0.75rem;
    }

    .method-badge {
        font-size: 0.6rem;
        font-weight: 700;
        padding: 0.2rem 0.5rem;
        letter-spacing: 0.05em;
    }

    .method-badge.post {
        background: rgba(59, 130, 246, 0.12);
        color: #60a5fa;
        border: 1px solid rgba(59, 130, 246, 0.25);
    }

    .method-badge.put {
        background: rgba(251, 191, 36, 0.12);
        color: #fbbf24;
        border: 1px solid rgba(251, 191, 36, 0.25);
    }

    .method-badge.patch {
        background: rgba(168, 85, 247, 0.12);
        color: #c084fc;
        border: 1px solid rgba(168, 85, 247, 0.25);
    }

    .request-url {
        font-size: 0.72rem;
        color: var(--text-secondary);
        word-break: break-all;
        flex: 1;
    }

    .request-time {
        font-size: 0.62rem;
        color: var(--text-dim);
        flex-shrink: 0;
    }

    /* Collapsible sections */
    .request-section {
        margin-top: 0.5rem;
    }

    .section-toggle {
        font-family: 'JetBrains Mono', monospace;
        font-size: 0.65rem;
        font-weight: 500;
        color: var(--text-dim);
        background: none;
        border: none;
        cursor: pointer;
        padding: 0.25rem 0;
        display: flex;
        align-items: center;
        gap: 0.4rem;
        transition: color 0.15s ease;
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
        margin-top: 0.4rem;
        padding: 0.75rem;
        background: var(--bg-elevated);
        border: 1px solid var(--border);
        overflow-x: auto;
    }

    .section-content.show {
        display: block;
    }

    /* JSON syntax in request cards */
    .json-block {
        font-size: 0.72rem;
        line-height: 1.7;
        white-space: pre-wrap;
        word-break: break-all;
    }

    .syn-key { color: var(--syn-key); }
    .syn-string { color: var(--syn-string); }
    .syn-number { color: var(--syn-number); }
    .syn-bool { color: var(--syn-bool); }
    .syn-null { color: var(--syn-null); font-style: italic; }
    .syn-bracket { color: var(--syn-bracket); }
    .syn-colon { color: var(--syn-colon); }
    .syn-comma { color: var(--syn-colon); }

    /* Headers table */
    .headers-table {
        width: 100%;
        font-size: 0.68rem;
    }

    .headers-table tr {
        border-bottom: 1px solid var(--border);
    }

    .headers-table tr:last-child {
        border-bottom: none;
    }

    .headers-table td {
        padding: 0.3rem 0;
        vertical-align: top;
    }

    .headers-table td:first-child {
        color: var(--accent);
        font-weight: 500;
        padding-right: 1rem;
        white-space: nowrap;
    }

    .headers-table td:last-child {
        color: var(--text-secondary);
        word-break: break-all;
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

    .status-dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        display: inline-block;
        margin-right: 0.35rem;
        background: var(--accent);
        box-shadow: 0 0 6px rgba(87, 64, 153, 0.4);
    }

    /* Copy feedback */
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
        .info-panel {
            border-right: none;
            border-bottom: 1px solid var(--border);
        }
        .feed-body {
            min-height: 50vh;
        }
        .feed-empty {
            min-height: 40vh;
        }
    }
@endsection

@section('actions')
    <button class="btn btn-danger" onclick="clearRequests()">clear all</button>
@endsection

@section('content')
    <main class="main">
        <div class="info-panel">
            <div class="pane-header">
                <div class="pane-title active">webhook url</div>
            </div>
            <div class="info-body">
                <div class="info-section">
                    <div class="info-label">Send requests to</div>
                    <div class="webhook-url-box">
                        <code id="webhook-url">{{ url('/webhook') }}</code>
                        <button class="copy-btn" onclick="copyWebhookUrl()" id="copy-url-btn">copy</button>
                    </div>
                    <div class="method-list">
                        <span class="method-chip">POST</span>
                        <span class="method-chip">PUT</span>
                        <span class="method-chip">PATCH</span>
                    </div>
                </div>

                <div class="info-section">
                    <div class="info-label">Try it</div>
                    <div class="info-hint">
                        <code>curl -X POST {{ url('/webhook/test') }} \<br>
                        &nbsp;&nbsp;-H "Content-Type: application/json" \<br>
                        &nbsp;&nbsp;-d '{"hello":"world"}'</code>
                    </div>
                </div>

                <div class="info-section">
                    <div class="info-label">Wildcard paths</div>
                    <div class="info-hint">
                        Any path under <code>/webhook/</code> is captured:<br>
                        <code>/webhook/stripe</code><br>
                        <code>/webhook/github/push</code><br>
                        <code>/webhook/anything/you/want</code>
                    </div>
                </div>
            </div>
        </div>

        <div class="feed-panel">
            <div class="pane-header">
                <div class="pane-title" id="feed-title">live feed</div>
                <div class="pane-meta" id="feed-meta">
                    @if($requests->count() > 0)
                        {{ $requests->count() }} request{{ $requests->count() !== 1 ? 's' : '' }}
                    @endif
                </div>
            </div>
            <div class="feed-body" id="feed-body">
                @if($requests->isEmpty())
                    <div class="feed-empty" id="feed-empty">
                        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M22 12h-4l-3 9L9 3l-3 9H2"/>
                        </svg>
                        <p><span class="pulse-dot"></span> Listening for requests...<br>Send a webhook to see it here</p>
                    </div>
                @else
                    @foreach($requests as $req)
                        <div class="request-card" data-id="{{ $req->id }}">
                            @include('partials._request-card', ['req' => $req])
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    </main>

    <div class="statusbar">
        <div>
            <span class="status-dot"></span>
            <span id="poll-status">polling every 1.5s</span>
        </div>
        <div id="request-count">{{ $requests->count() }} requests captured</div>
    </div>

    <div class="copy-feedback" id="copy-feedback">Copied to clipboard</div>
@endsection

@section('scripts')
    <script>
        let pollTimer = null;
        let lastId = {{ $requests->first()?->id ?? 0 }};
        let totalCount = {{ $requests->count() }};

        function startPolling() {
            pollTimer = setInterval(async () => {
                try {
                    const resp = await fetch(`/webhook/poll?after_id=${lastId}`);
                    const data = await resp.json();

                    if (data.requests && data.requests.length > 0) {
                        if (data.last_id) lastId = data.last_id;
                        totalCount += data.requests.length;

                        const feedBody = document.getElementById('feed-body');
                        const empty = document.getElementById('feed-empty');
                        if (empty) empty.remove();

                        data.requests.forEach(req => {
                            const card = document.createElement('div');
                            card.className = 'request-card';
                            card.dataset.id = req.id;
                            card.innerHTML = buildRequestCard(req);
                            feedBody.prepend(card);
                        });

                        document.getElementById('feed-meta').textContent =
                            `${totalCount} request${totalCount !== 1 ? 's' : ''}`;
                        document.getElementById('request-count').textContent =
                            `${totalCount} requests captured`;
                        document.getElementById('feed-title').classList.add('active');
                    }
                } catch (e) {
                    // silently retry next interval
                }
            }, 1500);
        }

        function buildRequestCard(req) {
            const method = req.method.toLowerCase();
            const time = new Date(req.received_at).toLocaleTimeString();
            const url = req.url;

            let headersHtml = '';
            if (req.headers && typeof req.headers === 'object') {
                headersHtml = '<table class="headers-table">';
                for (const [key, values] of Object.entries(req.headers)) {
                    const val = Array.isArray(values) ? values.join(', ') : values;
                    headersHtml += `<tr><td>${escapeHtml(key)}</td><td>${escapeHtml(val)}</td></tr>`;
                }
                headersHtml += '</table>';
            }

            let bodyHtml = '';
            if (req.body && req.body.trim()) {
                let formatted = req.body;
                try {
                    const parsed = JSON.parse(req.body);
                    formatted = JSON.stringify(parsed, null, 2);
                    bodyHtml = `<div class="json-block">${syntaxHighlight(escapeHtml(formatted))}</div>`;
                } catch (e) {
                    bodyHtml = `<div class="json-block">${escapeHtml(formatted)}</div>`;
                }
            }

            const cardId = req.id;

            return `
                <div class="request-header">
                    <span class="method-badge ${method}">${req.method}</span>
                    <span class="request-url">${escapeHtml(url)}</span>
                    <span class="request-time">${time}</span>
                </div>
                ${headersHtml ? `
                <div class="request-section">
                    <button class="section-toggle" onclick="toggleSection(this, 'headers-${cardId}')">
                        <span class="arrow">&#9654;</span> headers
                    </button>
                    <div class="section-content" id="headers-${cardId}">${headersHtml}</div>
                </div>` : ''}
                ${bodyHtml ? `
                <div class="request-section">
                    <button class="section-toggle open" onclick="toggleSection(this, 'body-${cardId}')">
                        <span class="arrow">&#9654;</span> body
                    </button>
                    <div class="section-content show" id="body-${cardId}">${bodyHtml}</div>
                </div>` : ''}
            `;
        }

        function toggleSection(btn, targetId) {
            btn.classList.toggle('open');
            document.getElementById(targetId).classList.toggle('show');
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

        function copyWebhookUrl() {
            const url = document.getElementById('webhook-url').textContent;
            navigator.clipboard.writeText(url).then(() => {
                const btn = document.getElementById('copy-url-btn');
                btn.textContent = 'copied';
                btn.classList.add('copied');
                setTimeout(() => {
                    btn.textContent = 'copy';
                    btn.classList.remove('copied');
                }, 1500);
            });
        }

        async function clearRequests() {
            try {
                await fetch('/webhook/clear', {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    },
                });

                totalCount = 0;
                document.getElementById('feed-body').innerHTML = `
                    <div class="feed-empty" id="feed-empty">
                        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M22 12h-4l-3 9L9 3l-3 9H2"/>
                        </svg>
                        <p><span class="pulse-dot"></span> Listening for requests...<br>Send a webhook to see it here</p>
                    </div>
                `;
                document.getElementById('feed-meta').textContent = '';
                document.getElementById('request-count').textContent = '0 requests captured';
                document.getElementById('feed-title').classList.remove('active');
                lastId = 0;
            } catch (e) {
                // silent fail
            }
        }

        // Highlight server-rendered JSON blocks
        document.querySelectorAll('[data-json-highlight]').forEach(el => {
            el.innerHTML = syntaxHighlight(escapeHtml(el.textContent));
        });

        // Start polling on page load
        startPolling();
    </script>
@endsection
