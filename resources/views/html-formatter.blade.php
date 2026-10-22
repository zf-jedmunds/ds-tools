@extends('layouts.app')

@section('title', 'HTML Formatter')

@section('styles')
    /* Syntax colors */
    :root {
        --syn-tag: #9b7fdb;
        --syn-attr: #fbbf24;
        --syn-string: #7dd3fc;
        --syn-comment: #555568;
        --syn-keyword: #9b7fdb;
        --syn-bracket: #636380;
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
    #html-input {
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

    #html-input::placeholder {
        color: var(--text-dim);
    }

    #html-input:focus {
        background: rgba(87, 64, 153, 0.01);
    }

    /* Output */
    #html-output {
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

    /* Syntax highlighted HTML */
    .html-line {
        display: block;
        font-size: 0.82rem;
        line-height: 1.7;
        white-space: pre;
        animation: fadeSlideIn 0.3s ease both;
    }

    .html-line:hover {
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

    .syn-tag { color: var(--syn-tag); }
    .syn-attr { color: var(--syn-attr); }
    .syn-string { color: var(--syn-string); }
    .syn-comment { color: var(--syn-comment); font-style: italic; }
    .syn-keyword { color: var(--syn-keyword); }
    .syn-bracket { color: var(--syn-bracket); }

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

    /* Savings */
    .savings {
        font-size: 0.65rem;
        color: var(--accent);
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
        #html-input,
        #html-output {
            min-height: 40vh;
        }
        .output-placeholder {
            min-height: 30vh;
        }
    }
@endsection

@section('actions')
    <button class="btn" onclick="loadSample()">sample</button>
    <button class="btn" onclick="minifyHtml()">minify</button>
    <button class="btn" onclick="copyOutput()">copy <kbd>&#8984;C</kbd></button>
    <button class="btn btn-danger" onclick="clearAll()">clear</button>
    <button class="btn btn-primary" onclick="formatHtml()">format <kbd>&#8984;&crarr;</kbd></button>
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
                    id="html-input"
                    placeholder='Paste your HTML here...

<div class="container"><h1>Hello</h1><p>World</p></div>'
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
                <div id="html-output">
                    <div class="output-placeholder">
                        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="16 18 22 12 16 6"/>
                            <polyline points="8 6 2 12 8 18"/>
                        </svg>
                        <p>Paste HTML on the left and press<br><kbd>Format</kbd> or <kbd>&#8984; Enter</kbd></p>
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
            <span class="savings" id="savings"></span>
            <div class="indent-controls">
                <span>indent:</span>
                <button class="indent-btn" data-indent="2" onclick="setIndent(2)">2</button>
                <button class="indent-btn" data-indent="3" onclick="setIndent(3)">3</button>
                <button class="indent-btn active" data-indent="4" onclick="setIndent(4)">4</button>
                <button class="indent-btn" data-indent="tab" onclick="setIndent('tab')">tab</button>
            </div>
        </div>
    </div>

    <div class="copy-feedback" id="copy-feedback">Copied to clipboard</div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/js-beautify@1/js/lib/beautify-html.min.js"></script>
    <script>
        let currentIndent = 4;
        let lastOutput = null;

        const input = document.getElementById('html-input');
        const output = document.getElementById('html-output');
        const errorContainer = document.getElementById('error-container');
        const statusDot = document.getElementById('status-dot');
        const statusText = document.getElementById('status-text');
        const inputMeta = document.getElementById('input-meta');
        const outputMeta = document.getElementById('output-meta');
        const outputTitle = document.getElementById('output-title');
        const charCount = document.getElementById('char-count');
        const savingsEl = document.getElementById('savings');

        // Auto-format on paste
        input.addEventListener('paste', () => {
            setTimeout(() => {
                formatHtml();
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
                formatHtml();
            }
        });

        // Tab support in textarea
        input.addEventListener('keydown', (e) => {
            if (e.key === 'Tab') {
                e.preventDefault();
                const start = input.selectionStart;
                const end = input.selectionEnd;
                const indent = currentIndent === 'tab' ? '\t' : ' '.repeat(currentIndent);
                input.value = input.value.substring(0, start) + indent + input.value.substring(end);
                input.selectionStart = input.selectionEnd = start + indent.length;
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

        function formatHtml() {
            const raw = input.value.trim();
            if (!raw) return;

            errorContainer.innerHTML = '';
            savingsEl.textContent = '';

            try {
                const indentSize = currentIndent === 'tab' ? 1 : currentIndent;
                const indentChar = currentIndent === 'tab' ? '\t' : ' ';
                const formatted = html_beautify(raw, {
                    indent_size: indentSize,
                    indent_char: indentChar,
                    wrap_line_length: 0,
                    preserve_newlines: true,
                    max_preserve_newlines: 2,
                    indent_inner_html: true
                });

                lastOutput = formatted;
                renderHighlighted(formatted);
                setStatus('valid', 'formatted');
                outputTitle.classList.add('active');

                const lines = formatted.split('\n').length;
                const bytes = new Blob([formatted]).size;
                outputMeta.textContent = `${lines} lines \u00b7 ${formatBytes(bytes)}`;
            } catch (e) {
                setStatus('invalid', 'error');
                outputTitle.classList.remove('active');
                showError(e.message);
            }

            updateInputMeta();
        }

        function minifyHtml() {
            const raw = input.value.trim();
            if (!raw) return;

            errorContainer.innerHTML = '';

            try {
                const originalSize = new Blob([raw]).size;

                // Remove HTML comments
                let minified = raw.replace(/<!--[\s\S]*?-->/g, '');
                // Collapse whitespace between tags
                minified = minified.replace(/>\s+</g, '><');
                // Collapse other whitespace
                minified = minified.replace(/\s+/g, ' ');
                // Trim
                minified = minified.trim();

                lastOutput = minified;
                renderHighlighted(minified);
                setStatus('valid', 'minified');
                outputTitle.classList.add('active');

                const resultSize = new Blob([minified]).size;
                const lines = minified.split('\n').length;
                outputMeta.textContent = `${lines} ${lines === 1 ? 'line' : 'lines'} \u00b7 ${formatBytes(resultSize)}`;

                const diff = originalSize - resultSize;
                const pct = originalSize > 0 ? ((diff / originalSize) * 100).toFixed(1) : 0;
                if (diff > 0) {
                    savingsEl.textContent = `\u2212${formatBytes(diff)} (${pct}% smaller)`;
                } else if (diff < 0) {
                    savingsEl.textContent = `+${formatBytes(Math.abs(diff))} (${Math.abs(pct)}% larger)`;
                } else {
                    savingsEl.textContent = 'same size';
                }
            } catch (e) {
                setStatus('invalid', 'error');
                outputTitle.classList.remove('active');
                showError(e.message);
            }

            updateInputMeta();
        }

        function renderHighlighted(html) {
            const lines = html.split('\n');
            let result = '';

            lines.forEach((line, i) => {
                const delay = Math.min(i * 8, 400);
                const highlighted = highlightHtml(escapeHtml(line));
                result += `<span class="html-line" style="animation-delay:${delay}ms"><span class="line-number">${i + 1}</span>${highlighted}</span>`;
            });

            output.innerHTML = result;
        }

        function highlightHtml(str) {
            return str
                // Comments <!--...-->
                .replace(/(&lt;!--[\s\S]*?--&gt;)/g, '<span class="syn-comment">$1</span>')
                // DOCTYPE
                .replace(/(&lt;!DOCTYPE[^&]*?&gt;)/gi, '<span class="syn-keyword">$1</span>')
                // Closing tags </tagname>
                .replace(/(&lt;\/)([a-zA-Z][a-zA-Z0-9-]*)(&gt;)/g, '<span class="syn-bracket">$1</span><span class="syn-tag">$2</span><span class="syn-bracket">$3</span>')
                // Self-closing /> and >
                .replace(/(\/&gt;)/g, '<span class="syn-bracket">$1</span>')
                // Opening tags with attributes: <tagname ...>
                .replace(/(&lt;)([a-zA-Z][a-zA-Z0-9-]*)((?:\s+[^&]*?)?)(&gt;)/g, function(match, open, tag, attrs, close) {
                    const highlightedAttrs = attrs
                        // Attribute with double-quoted value
                        .replace(/([a-zA-Z_:][a-zA-Z0-9_.:-]*)(\s*=\s*)(&quot;[^&]*?&quot;)/g, '<span class="syn-attr">$1</span>$2<span class="syn-string">$3</span>')
                        // Attribute with single-quoted value
                        .replace(/([a-zA-Z_:][a-zA-Z0-9_.:-]*)(\s*=\s*)(&#39;[^&]*?&#39;)/g, '<span class="syn-attr">$1</span>$2<span class="syn-string">$3</span>')
                        // Boolean attributes (no value)
                        .replace(/\s([a-zA-Z_:][a-zA-Z0-9_.:-]*)(?=\s|$)/g, ' <span class="syn-attr">$1</span>');
                    return '<span class="syn-bracket">' + open + '</span><span class="syn-tag">' + tag + '</span>' + highlightedAttrs + '<span class="syn-bracket">' + close + '</span>';
                })
                // Handle opening tags without closing > on same line (multiline attributes)
                .replace(/(&lt;)([a-zA-Z][a-zA-Z0-9-]*)((?:\s+[^&]*?)?)$/g, function(match, open, tag, attrs) {
                    const highlightedAttrs = attrs
                        .replace(/([a-zA-Z_:][a-zA-Z0-9_.:-]*)(\s*=\s*)(&quot;[^&]*?&quot;)/g, '<span class="syn-attr">$1</span>$2<span class="syn-string">$3</span>')
                        .replace(/([a-zA-Z_:][a-zA-Z0-9_.:-]*)(\s*=\s*)(&#39;[^&]*?&#39;)/g, '<span class="syn-attr">$1</span>$2<span class="syn-string">$3</span>')
                        .replace(/\s([a-zA-Z_:][a-zA-Z0-9_.:-]*)(?=\s|$)/g, ' <span class="syn-attr">$1</span>');
                    return '<span class="syn-bracket">' + open + '</span><span class="syn-tag">' + tag + '</span>' + highlightedAttrs;
                });
        }

        function showError(message) {
            errorContainer.innerHTML = `
                <div class="error-banner">
                    <div class="error-icon">&#x2717;</div>
                    <div class="error-content">
                        <div class="error-title">Format Error</div>
                        <div class="error-message">${escapeHtml(message)}</div>
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
            if (lastOutput !== null) {
                formatHtml();
            }
        }

        function copyOutput() {
            if (!lastOutput) return;
            navigator.clipboard.writeText(lastOutput).then(() => {
                const feedback = document.getElementById('copy-feedback');
                feedback.classList.add('show');
                setTimeout(() => feedback.classList.remove('show'), 1500);
            });
        }

        function clearAll() {
            input.value = '';
            lastOutput = null;
            errorContainer.innerHTML = '';
            output.innerHTML = `
                <div class="output-placeholder">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="16 18 22 12 16 6"/>
                        <polyline points="8 6 2 12 8 18"/>
                    </svg>
                    <p>Paste HTML on the left and press<br><kbd>Format</kbd> or <kbd>\u2318 Enter</kbd></p>
                </div>
            `;
            setStatus('', 'ready');
            inputMeta.textContent = '';
            outputMeta.textContent = '';
            charCount.textContent = '';
            savingsEl.textContent = '';
            outputTitle.classList.remove('active');
        }

        function loadSample() {
            input.value = `<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Dashboard</title><link rel="stylesheet" href="/css/app.css"></head>
<body><div class="app"><header class="header"><nav class="nav"><a href="/" class="nav-logo">Acme</a><ul class="nav-links"><li><a href="/dashboard">Dashboard</a></li><li><a href="/settings">Settings</a></li></ul></nav></header><main class="content"><section class="hero"><h1>Welcome back, developer</h1><p>Here's what's happening with your projects today.</p></section><div class="grid"><div class="card"><h2 class="card-title">Analytics</h2><p class="card-value">12,847</p><span class="card-label">Total views this week</span></div><div class="card"><h2 class="card-title">Revenue</h2><p class="card-value">$4,521</p><span class="card-label">Monthly recurring</span></div><!-- More cards here --></div></main><footer><p>&copy; 2026 Acme Inc. All rights reserved.</p></footer></div><` + `script src="/js/app.js"></` + `script></body>
</html>`;
            updateInputMeta();
            formatHtml();
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
