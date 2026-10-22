@extends('layouts.app')

@section('title', 'Minifier')

@section('styles')
    /* Syntax colors */
    :root {
        --syn-keyword: #9b7fdb;
        --syn-string: #7dd3fc;
        --syn-number: #f0abfc;
        --syn-comment: #555568;
        --syn-function: #fbbf24;
        --syn-operator: #636380;
        --syn-property: #7dd3fc;
        --syn-selector: #9b7fdb;
        --syn-value: #f0abfc;
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

    .pane-header-left {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    /* Language toggle */
    .lang-controls {
        display: flex;
        align-items: center;
        gap: 0.25rem;
    }

    .lang-btn {
        font-family: 'JetBrains Mono', monospace;
        font-size: 0.65rem;
        padding: 0.15rem 0.45rem;
        background: transparent;
        border: 1px solid var(--border);
        color: var(--text-dim);
        cursor: pointer;
        transition: all 0.15s ease;
    }

    .lang-btn:hover {
        border-color: var(--border-focus);
        color: var(--text-secondary);
    }

    .lang-btn.active {
        background: var(--accent);
        border-color: var(--accent);
        color: var(--bg-deep);
        font-weight: 600;
    }

    /* Input textarea */
    #code-input {
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

    #code-input::placeholder {
        color: var(--text-dim);
    }

    #code-input:focus {
        background: rgba(87, 64, 153, 0.01);
    }

    /* Output */
    #code-output {
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

    /* Syntax highlighted code */
    .code-line {
        display: block;
        font-size: 0.82rem;
        line-height: 1.7;
        white-space: pre;
        animation: fadeSlideIn 0.3s ease both;
    }

    .code-line:hover {
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

    .syn-keyword { color: var(--syn-keyword); }
    .syn-string { color: var(--syn-string); }
    .syn-number { color: var(--syn-number); }
    .syn-comment { color: var(--syn-comment); font-style: italic; }
    .syn-function { color: var(--syn-function); }
    .syn-operator { color: var(--syn-operator); }
    .syn-property { color: var(--syn-property); }
    .syn-selector { color: var(--syn-selector); }
    .syn-value { color: var(--syn-value); }
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

    /* Size savings */
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
        #code-input,
        #code-output {
            min-height: 40vh;
        }
        .output-placeholder {
            min-height: 30vh;
        }
    }
@endsection

@section('actions')
    <button class="btn" onclick="loadSample()" title="Load sample code">sample</button>
    <button class="btn" onclick="beautifyCode()">beautify</button>
    <button class="btn" onclick="copyOutput()">copy <kbd>&#8984;C</kbd></button>
    <button class="btn btn-danger" onclick="clearAll()">clear</button>
    <button class="btn btn-primary" onclick="minifyCode()">minify <kbd>&#8984;&crarr;</kbd></button>
@endsection

@section('content')
    <main class="main">
        <div class="pane">
            <div class="pane-header">
                <div class="pane-header-left">
                    <div class="pane-title active">input</div>
                    <div class="lang-controls">
                        <button class="lang-btn active" data-lang="js" onclick="setLang('js')">JS</button>
                        <button class="lang-btn" data-lang="css" onclick="setLang('css')">CSS</button>
                    </div>
                </div>
                <div class="pane-meta" id="input-meta"></div>
            </div>
            <div class="pane-body">
                <textarea
                    id="code-input"
                    placeholder="Paste your JavaScript or CSS here..."
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
                <div id="code-output">
                    <div class="output-placeholder">
                        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M4 8V4a2 2 0 0 1 2-2h2"/>
                            <path d="M4 16v4a2 2 0 0 0 2 2h2"/>
                            <path d="M16 2h2a2 2 0 0 1 2 2v4"/>
                            <path d="M16 22h2a2 2 0 0 0 2-2v-4"/>
                            <path d="M9 10h6"/>
                            <path d="M12 13V7"/>
                        </svg>
                        <p>Paste code on the left and press<br><kbd>Minify</kbd> or <kbd>&#8984; Enter</kbd></p>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <div class="statusbar">
        <div class="statusbar-left">
            <span><span class="status-dot" id="status-dot"></span><span id="status-text">ready</span></span>
        </div>
        <div class="statusbar-right">
            <span class="savings" id="savings"></span>
        </div>
    </div>

    <div class="copy-feedback" id="copy-feedback">Copied to clipboard</div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/terser@5/dist/bundle.min.js"></script>
    <script>
        let currentLang = 'js';
        let lastOutput = null;

        const input = document.getElementById('code-input');
        const output = document.getElementById('code-output');
        const errorContainer = document.getElementById('error-container');
        const statusDot = document.getElementById('status-dot');
        const statusText = document.getElementById('status-text');
        const inputMeta = document.getElementById('input-meta');
        const outputMeta = document.getElementById('output-meta');
        const outputTitle = document.getElementById('output-title');
        const savingsEl = document.getElementById('savings');

        // Auto-minify on paste
        input.addEventListener('paste', () => {
            setTimeout(() => {
                updateInputMeta();
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
                minifyCode();
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

        function setLang(lang) {
            currentLang = lang;
            document.querySelectorAll('.lang-btn').forEach(btn => {
                btn.classList.toggle('active', btn.dataset.lang === lang);
            });
        }

        function updateInputMeta() {
            const len = input.value.length;
            if (len === 0) {
                inputMeta.textContent = '';
                return;
            }
            const lines = input.value.split('\n').length;
            inputMeta.textContent = `${len.toLocaleString()} chars \u00b7 ${lines} lines`;
        }

        // --- JS minification / beautification ---

        async function minifyJs(code) {
            const result = await Terser.minify(code, {
                compress: true,
                mangle: true,
                output: { comments: false }
            });
            if (result.error) throw result.error;
            return result.code;
        }

        async function beautifyJs(code) {
            const result = await Terser.minify(code, {
                compress: false,
                mangle: false,
                output: { beautify: true, indent_level: 2, comments: true }
            });
            if (result.error) throw result.error;
            return result.code;
        }

        // --- CSS minification / beautification ---

        function minifyCss(code) {
            return code
                .replace(/\/\*[\s\S]*?\*\//g, '')
                .replace(/\s+/g, ' ')
                .replace(/\s*{\s*/g, '{')
                .replace(/\s*}\s*/g, '}')
                .replace(/\s*:\s*/g, ':')
                .replace(/\s*;\s*/g, ';')
                .replace(/\s*,\s*/g, ',')
                .replace(/;}/g, '}')
                .trim();
        }

        function beautifyCss(code) {
            // First normalize to minified form
            let normalized = minifyCss(code);
            let result = '';
            let depth = 0;
            const indent = () => '  '.repeat(depth);

            for (let i = 0; i < normalized.length; i++) {
                const ch = normalized[i];

                if (ch === '{') {
                    result += ' {\n';
                    depth++;
                    result += indent();
                } else if (ch === '}') {
                    depth = Math.max(0, depth - 1);
                    result += '\n' + indent() + '}\n';
                    if (depth === 0) result += '\n';
                    if (i + 1 < normalized.length && normalized[i + 1] !== '}') {
                        result += indent();
                    }
                } else if (ch === ';') {
                    result += ';\n' + indent();
                } else if (ch === ':' && !isInsideValue(normalized, i)) {
                    result += ': ';
                } else if (ch === ',') {
                    result += ',\n' + indent();
                } else {
                    result += ch;
                }
            }

            // Clean up extra whitespace on empty lines
            return result.replace(/^\s+$/gm, '').replace(/\n{3,}/g, '\n\n').trim();
        }

        function isInsideValue(str, colonPos) {
            // Check if this colon is inside a value (e.g. url(...), pseudo-selectors)
            // Look backwards from colonPos for the nearest { or ;
            for (let j = colonPos - 1; j >= 0; j--) {
                if (str[j] === '{' || str[j] === ';') return false;
                if (str[j] === ':') return true;
                if (str[j] === '}') return true;
            }
            return false;
        }

        // --- Main actions ---

        async function minifyCode() {
            const raw = input.value.trim();
            if (!raw) return;

            errorContainer.innerHTML = '';

            try {
                let result;
                if (currentLang === 'js') {
                    result = await minifyJs(raw);
                } else {
                    result = minifyCss(raw);
                }

                lastOutput = result;
                renderHighlighted(result, currentLang);
                setStatus('valid', 'minified');
                outputTitle.classList.add('active');
                showSavings(raw, result);

                const lines = result.split('\n').length;
                const bytes = new Blob([result]).size;
                outputMeta.textContent = `${lines} ${lines === 1 ? 'line' : 'lines'} \u00b7 ${formatBytes(bytes)}`;
            } catch (e) {
                setStatus('invalid', 'error');
                outputTitle.classList.remove('active');
                showError(e.message || String(e));
            }

            updateInputMeta();
        }

        async function beautifyCode() {
            const raw = input.value.trim();
            if (!raw) return;

            errorContainer.innerHTML = '';

            try {
                let result;
                if (currentLang === 'js') {
                    result = await beautifyJs(raw);
                } else {
                    result = beautifyCss(raw);
                }

                lastOutput = result;
                renderHighlighted(result, currentLang);
                setStatus('valid', 'beautified');
                outputTitle.classList.add('active');
                showSavings(raw, result);

                const lines = result.split('\n').length;
                const bytes = new Blob([result]).size;
                outputMeta.textContent = `${lines} ${lines === 1 ? 'line' : 'lines'} \u00b7 ${formatBytes(bytes)}`;
            } catch (e) {
                setStatus('invalid', 'error');
                outputTitle.classList.remove('active');
                showError(e.message || String(e));
            }

            updateInputMeta();
        }

        // --- Syntax highlighting ---

        function highlightJs(str) {
            return str
                // Comments (single-line)
                .replace(/(\/\/.*)/g, '<span class="syn-comment">$1</span>')
                // Strings (double-quoted)
                .replace(/(&quot;[^&]*?&quot;)/g, '<span class="syn-string">$1</span>')
                // Strings (single-quoted)
                .replace(/(&#39;[^&]*?&#39;)/g, '<span class="syn-string">$1</span>')
                // Template literals
                .replace(/(`[^`]*?`)/g, '<span class="syn-string">$1</span>')
                // Keywords
                .replace(/\b(const|let|var|function|return|if|else|for|while|do|switch|case|break|continue|new|this|class|extends|import|export|default|from|async|await|try|catch|finally|throw|typeof|instanceof|in|of|yield|void|delete|null|undefined|true|false)\b/g, '<span class="syn-keyword">$1</span>')
                // Function calls
                .replace(/\b([a-zA-Z_$][\w$]*)\s*\(/g, '<span class="syn-function">$1</span>(')
                // Numbers
                .replace(/\b(\d+\.?\d*(?:[eE][+-]?\d+)?)\b/g, '<span class="syn-number">$1</span>')
                // Brackets
                .replace(/([[\]{}()])/g, '<span class="syn-bracket">$1</span>')
                // Operators
                .replace(/(===|!==|==|!=|&lt;=|&gt;=|&lt;|&gt;|&amp;&amp;|\|\||=>|\+\+|--)/g, '<span class="syn-operator">$1</span>');
        }

        function highlightCss(str) {
            return str
                // Comments
                .replace(/(\/\*[\s\S]*?\*\/)/g, '<span class="syn-comment">$1</span>')
                // Strings
                .replace(/(&quot;[^&]*?&quot;)/g, '<span class="syn-string">$1</span>')
                .replace(/(&#39;[^&]*?&#39;)/g, '<span class="syn-string">$1</span>')
                // Properties (word followed by colon)
                .replace(/\b([\w-]+)\s*:/g, '<span class="syn-property">$1</span>:')
                // Numbers with units
                .replace(/\b(\d+\.?\d*)(px|em|rem|%|vh|vw|vmin|vmax|deg|s|ms|fr|ch|ex)\b/g, '<span class="syn-number">$1$2</span>')
                // Plain numbers
                .replace(/\b(\d+\.?\d*)\b/g, '<span class="syn-number">$1</span>')
                // Selectors (lines that end with {, roughly)
                .replace(/^(\s*)([\w.#\-\[\]:,>~+*\s]+)(\s*\{)/gm, '$1<span class="syn-selector">$2</span>$3')
                // Color values
                .replace(/(#[0-9a-fA-F]{3,8})\b/g, '<span class="syn-value">$1</span>')
                // Brackets
                .replace(/([{}])/g, '<span class="syn-bracket">$1</span>');
        }

        function renderHighlighted(code, lang) {
            const lines = code.split('\n');
            let html = '';

            lines.forEach((line, i) => {
                const delay = Math.min(i * 8, 400);
                const escaped = escapeHtml(line);
                const highlighted = lang === 'js' ? highlightJs(escaped) : highlightCss(escaped);
                html += `<span class="code-line" style="animation-delay:${delay}ms"><span class="line-number">${i + 1}</span>${highlighted}</span>`;
            });

            output.innerHTML = html;
        }

        // --- Utilities ---

        function showSavings(original, result) {
            const originalSize = new Blob([original]).size;
            const resultSize = new Blob([result]).size;
            const diff = originalSize - resultSize;
            const pct = originalSize > 0 ? ((diff / originalSize) * 100).toFixed(1) : 0;

            if (diff > 0) {
                savingsEl.textContent = `\u2212${formatBytes(diff)} (${pct}% smaller)`;
            } else if (diff < 0) {
                savingsEl.textContent = `+${formatBytes(Math.abs(diff))} (${Math.abs(pct)}% larger)`;
            } else {
                savingsEl.textContent = 'same size';
            }
        }

        function showError(message) {
            errorContainer.innerHTML = `
                <div class="error-banner">
                    <div class="error-icon">&#x2717;</div>
                    <div class="error-content">
                        <div class="error-title">Parse Error</div>
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
                        <path d="M4 8V4a2 2 0 0 1 2-2h2"/>
                        <path d="M4 16v4a2 2 0 0 0 2 2h2"/>
                        <path d="M16 2h2a2 2 0 0 1 2 2v4"/>
                        <path d="M16 22h2a2 2 0 0 0 2-2v-4"/>
                        <path d="M9 10h6"/>
                        <path d="M12 13V7"/>
                    </svg>
                    <p>Paste code on the left and press<br><kbd>Minify</kbd> or <kbd>\u2318 Enter</kbd></p>
                </div>
            `;
            setStatus('', 'ready');
            inputMeta.textContent = '';
            outputMeta.textContent = '';
            savingsEl.textContent = '';
            outputTitle.classList.remove('active');
        }

        function loadSample() {
            if (currentLang === 'js') {
                input.value = `// Debounce utility function
function debounce(func, wait, immediate) {
  let timeout;
  return function executedFunction() {
    const context = this;
    const args = arguments;
    const later = function() {
      timeout = null;
      if (!immediate) func.apply(context, args);
    };
    const callNow = immediate && !timeout;
    clearTimeout(timeout);
    timeout = setTimeout(later, wait);
    if (callNow) func.apply(context, args);
  };
}

// Usage example
const handleResize = debounce(function() {
  console.log('Window resized');
  document.body.style.width = window.innerWidth + 'px';
}, 250);

window.addEventListener('resize', handleResize);`;
            } else {
                input.value = `/* Modern layout with container queries */
.dashboard {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
  gap: 1.5rem;
  padding: 2rem;
  max-width: 1400px;
  margin: 0 auto;
}

.card {
  background: #1a1a2e;
  border: 1px solid rgba(255, 255, 255, 0.06);
  border-radius: 0.5rem;
  padding: 1.5rem;
  transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.card:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
}

.card__title {
  font-size: 1.125rem;
  font-weight: 600;
  color: #e2e8f0;
  margin-bottom: 0.75rem;
}

.card__content {
  font-size: 0.875rem;
  line-height: 1.7;
  color: #94a3b8;
}

@media (max-width: 768px) {
  .dashboard {
    grid-template-columns: 1fr;
    padding: 1rem;
  }
}`;
            }

            updateInputMeta();
            minifyCode();
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
