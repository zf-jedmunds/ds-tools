@extends('layouts.app')

@section('title', 'JSON Prettifier')

@section('styles')
    /* Syntax colors */
    :root {
        --syn-key: #9b7fdb;
        --syn-string: #7dd3fc;
        --syn-number: #f0abfc;
        --syn-bool: #fbbf24;
        --syn-null: #6b7280;
        --syn-bracket: #636380;
        --syn-colon: #636380;
    }

    /* Main layout */
    .main {
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

    .pane-body {
        flex: 1;
        position: relative;
        background: var(--bg-panel);
    }

    /* Input textarea */
    #json-input {
        width: 100%;
        height: 100%;
        min-height: calc(100vh - 175px);
        background: transparent;
        border: none;
        color: var(--text-primary);
        font-family: 'JetBrains Mono', monospace;
        font-size: 0.82rem;
        line-height: 1.7;
        padding: 1.25rem;
        resize: none;
        outline: none;
        tab-size: 2;
    }

    #json-input::placeholder {
        color: var(--text-dim);
    }

    #json-input:focus {
        background: rgba(87, 64, 153, 0.01);
    }

    /* Output */
    #json-output {
        padding: 1.25rem;
        min-height: calc(100vh - 175px);
        overflow: auto;
    }

    .output-placeholder {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        height: 100%;
        min-height: calc(100vh - 250px);
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

    .output-placeholder kbd {
        font-family: 'JetBrains Mono', monospace;
        font-size: 0.68rem;
        padding: 0.15rem 0.45rem;
        background: var(--bg-elevated);
        border: 1px solid var(--border);
        color: var(--text-secondary);
    }

    /* Syntax highlighted JSON */
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
        width: 3.5em;
        text-align: right;
        padding-right: 1.5em;
        color: var(--text-dim);
        user-select: none;
        opacity: 0.5;
        font-size: 0.72rem;
    }

    @keyframes fadeSlideIn {
        from {
            opacity: 0;
            transform: translateY(2px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .syn-key { color: var(--syn-key); }
    .syn-string { color: var(--syn-string); }
    .syn-number { color: var(--syn-number); }
    .syn-bool { color: var(--syn-bool); }
    .syn-null { color: var(--syn-null); font-style: italic; }
    .syn-bracket { color: var(--syn-bracket); }
    .syn-colon { color: var(--syn-colon); }
    .syn-comma { color: var(--syn-colon); }

    /* Error state */
    .error-banner {
        padding: 1rem 1.25rem;
        background: var(--error-dim);
        border-bottom: 1px solid rgba(255, 77, 106, 0.15);
        display: flex;
        align-items: flex-start;
        gap: 0.75rem;
        animation: fadeSlideIn 0.2s ease;
    }

    .error-icon {
        color: var(--error);
        font-size: 0.85rem;
        margin-top: 0.1rem;
        flex-shrink: 0;
    }

    .error-content {
        flex: 1;
    }

    .error-title {
        font-size: 0.75rem;
        font-weight: 600;
        color: var(--error);
        margin-bottom: 0.25rem;
    }

    .error-message {
        font-size: 0.72rem;
        color: var(--text-secondary);
        line-height: 1.5;
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

    /* Indent controls */
    .indent-controls {
        display: flex;
        align-items: center;
        gap: 0.25rem;
    }

    .indent-btn {
        font-family: 'JetBrains Mono', monospace;
        font-size: 0.65rem;
        padding: 0.15rem 0.45rem;
        background: transparent;
        border: 1px solid var(--border);
        color: var(--text-dim);
        cursor: pointer;
        transition: all 0.15s ease;
    }

    .indent-btn:hover {
        border-color: var(--border-focus);
        color: var(--text-secondary);
    }

    .indent-btn.active {
        background: var(--accent);
        border-color: var(--accent);
        color: var(--bg-deep);
        font-weight: 600;
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

    /* Responsive */
    @media (max-width: 900px) {
        .main {
            grid-template-columns: 1fr;
            grid-template-rows: 1fr 1fr;
        }
        .pane:first-child {
            border-right: none;
            border-bottom: 1px solid var(--border);
        }
        #json-input,
        #json-output {
            min-height: 40vh;
        }
        .output-placeholder {
            min-height: 30vh;
        }
    }
@endsection

@section('actions')
    <button class="btn" onclick="loadSample()" title="Load sample JSON">sample</button>
    <button class="btn" onclick="minifyJson()">minify</button>
    <button class="btn" onclick="copyOutput()">copy <kbd>&#8984;C</kbd></button>
    <button class="btn btn-danger" onclick="clearAll()">clear</button>
    <button class="btn btn-primary" onclick="prettifyJson()">prettify <kbd>&#8984;&crarr;</kbd></button>
@endsection

@section('content')
    <main class="main">
        <div class="pane">
            <div class="pane-header">
                <div class="pane-title active">input</div>
                <div class="pane-meta" id="input-meta"></div>
            </div>
            <div class="pane-body">
                <textarea
                    id="json-input"
                    placeholder='Paste your JSON here...

{"name":"json prettifier","version":1,"features":["format","validate","highlight"]}'
                    spellcheck="false"
                    autocomplete="off"
                    autocorrect="off"
                    autocapitalize="off"
                ></textarea>
            </div>
        </div>

        <div class="pane">
            <div class="pane-header">
                <div class="pane-title" id="output-title">output</div>
                <div class="pane-meta" id="output-meta"></div>
            </div>
            <div class="pane-body">
                <div id="error-container"></div>
                <div id="json-output">
                    <div class="output-placeholder">
                        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M8 3H5a2 2 0 0 0-2 2v3m18 0V5a2 2 0 0 0-2-2h-3m0 18h3a2 2 0 0 0 2-2v-3M3 16v3a2 2 0 0 0 2 2h3"/>
                        </svg>
                        <p>Paste JSON on the left and press<br><kbd>Prettify</kbd> or <kbd>&#8984; Enter</kbd></p>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <div class="statusbar">
        <div class="statusbar-left">
            <span><span class="status-dot" id="status-dot"></span><span id="status-text">ready</span></span>
            <span id="char-count"></span>
        </div>
        <div class="statusbar-right">
            <div class="indent-controls">
                <span>indent:</span>
                <button class="indent-btn" data-indent="2" onclick="setIndent(2)">2</button>
                <button class="indent-btn active" data-indent="4" onclick="setIndent(4)">4</button>
                <button class="indent-btn" data-indent="tab" onclick="setIndent('tab')">tab</button>
            </div>
        </div>
    </div>

    <div class="copy-feedback" id="copy-feedback">Copied to clipboard</div>
@endsection

@section('scripts')
    <script>
        let currentIndent = 4;
        let lastValidJson = null;

        const input = document.getElementById('json-input');
        const output = document.getElementById('json-output');
        const errorContainer = document.getElementById('error-container');
        const statusDot = document.getElementById('status-dot');
        const statusText = document.getElementById('status-text');
        const inputMeta = document.getElementById('input-meta');
        const outputMeta = document.getElementById('output-meta');
        const outputTitle = document.getElementById('output-title');

        // Auto-prettify on paste
        input.addEventListener('paste', () => {
            setTimeout(() => {
                prettifyJson();
            }, 10);
        });

        // Update character count on input
        input.addEventListener('input', () => {
            updateInputMeta();
        });

        // Keyboard shortcuts
        document.addEventListener('keydown', (e) => {
            if ((e.metaKey || e.ctrlKey) && e.key === 'Enter') {
                e.preventDefault();
                prettifyJson();
            }
        });

        // Tab support in textarea
        input.addEventListener('keydown', (e) => {
            if (e.key === 'Tab') {
                e.preventDefault();
                const start = input.selectionStart;
                const end = input.selectionEnd;
                input.value = input.value.substring(0, start) + '  ' + input.value.substring(end);
                input.selectionStart = input.selectionEnd = start + 2;
            }
        });

        function updateInputMeta() {
            const len = input.value.length;
            if (len === 0) {
                inputMeta.textContent = '';
                return;
            }
            const lines = input.value.split('\n').length;
            inputMeta.textContent = `${len.toLocaleString()} chars \u00b7 ${lines} lines`;
        }

        function prettifyJson() {
            const raw = input.value.trim();
            if (!raw) return;

            errorContainer.innerHTML = '';

            try {
                const parsed = JSON.parse(raw);
                lastValidJson = parsed;
                const indent = currentIndent === 'tab' ? '\t' : currentIndent;
                const formatted = JSON.stringify(parsed, null, indent);
                renderHighlighted(formatted);
                setStatus('valid', 'valid json');
                outputTitle.classList.add('active');

                const lines = formatted.split('\n').length;
                const bytes = new Blob([formatted]).size;
                outputMeta.textContent = `${lines} lines \u00b7 ${formatBytes(bytes)}`;
            } catch (e) {
                setStatus('invalid', 'invalid json');
                outputTitle.classList.remove('active');
                showError(e.message);
            }

            updateInputMeta();
        }

        function minifyJson() {
            const raw = input.value.trim();
            if (!raw) return;

            errorContainer.innerHTML = '';

            try {
                const parsed = JSON.parse(raw);
                const minified = JSON.stringify(parsed);
                input.value = minified;
                renderHighlighted(minified);
                setStatus('valid', 'minified');
                outputTitle.classList.add('active');
                updateInputMeta();

                const bytes = new Blob([minified]).size;
                outputMeta.textContent = `1 line \u00b7 ${formatBytes(bytes)}`;
            } catch (e) {
                setStatus('invalid', 'invalid json');
                showError(e.message);
            }
        }

        function renderHighlighted(json) {
            const lines = json.split('\n');
            let html = '';

            lines.forEach((line, i) => {
                const delay = Math.min(i * 8, 400);
                const highlighted = syntaxHighlight(escapeHtml(line));
                html += `<span class="json-line" style="animation-delay:${delay}ms"><span class="line-number">${i + 1}</span>${highlighted}</span>`;
            });

            output.innerHTML = html;
        }

        function syntaxHighlight(str) {
            return str
                // Keys (quoted strings followed by colon)
                .replace(/&quot;([^&]*?)&quot;\s*:/g, '<span class="syn-key">&quot;$1&quot;</span><span class="syn-colon">:</span>')
                // String values
                .replace(/&quot;([^&]*?)&quot;/g, '<span class="syn-string">&quot;$1&quot;</span>')
                // Numbers
                .replace(/\b(-?\d+\.?\d*(?:[eE][+-]?\d+)?)\b/g, '<span class="syn-number">$1</span>')
                // Booleans
                .replace(/\b(true|false)\b/g, '<span class="syn-bool">$1</span>')
                // Null
                .replace(/\bnull\b/g, '<span class="syn-null">null</span>')
                // Brackets
                .replace(/([[\]{}])/g, '<span class="syn-bracket">$1</span>')
                // Commas
                .replace(/,/g, '<span class="syn-comma">,</span>');
        }

        function showError(message) {
            const posMatch = message.match(/position\s+(\d+)/i);
            let context = '';

            if (posMatch) {
                const pos = parseInt(posMatch[1]);
                const raw = input.value;
                const start = Math.max(0, pos - 25);
                const end = Math.min(raw.length, pos + 25);
                const before = escapeHtml(raw.substring(start, pos));
                const char = escapeHtml(raw.substring(pos, pos + 1) || 'EOF');
                const after = escapeHtml(raw.substring(pos + 1, end));
                context = `<br><br><span style="font-size:0.7rem;color:var(--text-dim)">${start > 0 ? '...' : ''}${before}<span style="color:var(--error);text-decoration:underline;font-weight:700">${char}</span>${after}${end < raw.length ? '...' : ''}</span>`;
            }

            errorContainer.innerHTML = `
                <div class="error-banner">
                    <div class="error-icon">&#x2717;</div>
                    <div class="error-content">
                        <div class="error-title">Parse Error</div>
                        <div class="error-message">${escapeHtml(message)}${context}</div>
                    </div>
                </div>
            `;

            output.innerHTML = `<div class="output-placeholder"><p style="color:var(--error)">Fix the error above and try again</p></div>`;
        }

        function setStatus(state, text) {
            statusDot.className = 'status-dot ' + state;
            statusText.textContent = text;
        }

        function setIndent(value) {
            currentIndent = value;
            document.querySelectorAll('.indent-btn').forEach(btn => {
                btn.classList.toggle('active', btn.dataset.indent == value);
            });
            if (lastValidJson !== null) {
                const indent = value === 'tab' ? '\t' : value;
                const formatted = JSON.stringify(lastValidJson, null, indent);
                renderHighlighted(formatted);
                const lines = formatted.split('\n').length;
                const bytes = new Blob([formatted]).size;
                outputMeta.textContent = `${lines} lines \u00b7 ${formatBytes(bytes)}`;
            }
        }

        function copyOutput() {
            if (!lastValidJson) return;
            const indent = currentIndent === 'tab' ? '\t' : currentIndent;
            const text = JSON.stringify(lastValidJson, null, indent);
            navigator.clipboard.writeText(text).then(() => {
                const feedback = document.getElementById('copy-feedback');
                feedback.classList.add('show');
                setTimeout(() => feedback.classList.remove('show'), 1500);
            });
        }

        function clearAll() {
            input.value = '';
            lastValidJson = null;
            errorContainer.innerHTML = '';
            output.innerHTML = `
                <div class="output-placeholder">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M8 3H5a2 2 0 0 0-2 2v3m18 0V5a2 2 0 0 0-2-2h-3m0 18h3a2 2 0 0 0 2-2v-3M3 16v3a2 2 0 0 0 2 2h3"/>
                    </svg>
                    <p>Paste JSON on the left and press<br><kbd>Prettify</kbd> or <kbd>\u2318 Enter</kbd></p>
                </div>
            `;
            setStatus('', 'ready');
            inputMeta.textContent = '';
            outputMeta.textContent = '';
            outputTitle.classList.remove('active');
        }

        function loadSample() {
            const sample = {
                "name": "JSON Prettifier",
                "version": "1.0.0",
                "description": "A tool for formatting and validating JSON data",
                "features": [
                    "syntax highlighting",
                    "error detection",
                    "configurable indentation",
                    "minification"
                ],
                "config": {
                    "defaultIndent": 4,
                    "maxDepth": null,
                    "strictMode": false,
                    "theme": "neon-noir"
                },
                "stats": {
                    "users": 48210,
                    "formatOperations": 1893422,
                    "uptime": 99.97
                },
                "tags": ["developer-tools", "json", "formatter"],
                "isPublic": true,
                "license": "MIT"
            };

            input.value = JSON.stringify(sample);
            updateInputMeta();
            prettifyJson();
        }

        function formatBytes(bytes) {
            if (bytes < 1024) return bytes + ' B';
            return (bytes / 1024).toFixed(1) + ' KB';
        }

        function escapeHtml(str) {
            const div = document.createElement('div');
            div.textContent = str;
            return div.innerHTML;
        }
    </script>
@endsection
