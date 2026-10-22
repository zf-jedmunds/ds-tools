@extends('layouts.app')

@section('title', 'Base64')

@section('styles')
    :root {
        --syn-string: #7dd3fc;
    }

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

    .pane-body.drag-over {
        outline: 2px dashed var(--accent);
        outline-offset: -4px;
        background: var(--accent-dim);
    }

    #b64-input {
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

    #b64-input::placeholder {
        color: var(--text-dim);
    }

    #b64-input:focus {
        background: rgba(87, 64, 153, 0.01);
    }

    #b64-output {
        padding: 1.25rem;
        min-height: calc(100vh - 175px);
        overflow: auto;
        font-family: 'JetBrains Mono', monospace;
        font-size: 0.82rem;
        line-height: 1.7;
        color: var(--syn-string);
        word-break: break-all;
        white-space: pre-wrap;
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

    .mode-controls {
        display: flex;
        align-items: center;
        gap: 0.25rem;
    }

    .mode-btn {
        font-family: 'JetBrains Mono', monospace;
        font-size: 0.65rem;
        padding: 0.15rem 0.45rem;
        background: transparent;
        border: 1px solid var(--border);
        color: var(--text-dim);
        cursor: pointer;
        transition: all 0.15s ease;
    }

    .mode-btn:hover {
        border-color: var(--border-focus);
        color: var(--text-secondary);
    }

    .mode-btn.active {
        background: var(--accent);
        border-color: var(--accent);
        color: var(--bg-deep);
        font-weight: 600;
    }

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
            grid-template-rows: 1fr 1fr;
        }
        .pane:first-child {
            border-right: none;
            border-bottom: 1px solid var(--border);
        }
        #b64-input,
        #b64-output {
            min-height: 40vh;
        }
        .output-placeholder {
            min-height: 30vh;
        }
    }
@endsection

@section('actions')
    <button class="btn btn-primary" id="btn-encode" onclick="setMode('encode')">encode</button>
    <button class="btn" id="btn-decode" onclick="setMode('decode')">decode</button>
    <button class="btn" onclick="copyOutput()">copy</button>
    <button class="btn btn-danger" onclick="clearAll()">clear</button>
@endsection

@section('content')
    <main class="main">
        <div class="pane">
            <div class="pane-header">
                <div class="pane-title active">input</div>
                <div class="pane-meta" id="input-meta"></div>
            </div>
            <div class="pane-body" id="input-pane-body">
                <textarea
                    id="b64-input"
                    placeholder="Paste text or drop a file here..."
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
                <div id="b64-output">
                    <div class="output-placeholder">
                        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M8 3H5a2 2 0 0 0-2 2v3m18 0V5a2 2 0 0 0-2-2h-3m0 18h3a2 2 0 0 0 2-2v-3M3 16v3a2 2 0 0 0 2 2h3"/>
                        </svg>
                        <p>Paste text or drop a file to encode/decode</p>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <div class="statusbar">
        <div class="statusbar-left">
            <span><span class="status-dot" id="status-dot"></span><span id="status-text">ready</span></span>
            <span id="size-info"></span>
        </div>
        <div class="statusbar-right">
            <div class="mode-controls">
                <span>mode:</span>
                <button class="mode-btn active" data-mode="encode" onclick="setMode('encode')">encode</button>
                <button class="mode-btn" data-mode="decode" onclick="setMode('decode')">decode</button>
            </div>
        </div>
    </div>

    <div class="copy-feedback" id="copy-feedback">Copied to clipboard</div>
@endsection

@section('scripts')
    <script>
        let mode = 'encode';
        let lastOutput = '';

        const input = document.getElementById('b64-input');
        const outputEl = document.getElementById('b64-output');
        const outputTitle = document.getElementById('output-title');
        const inputMeta = document.getElementById('input-meta');
        const outputMeta = document.getElementById('output-meta');
        const statusDot = document.getElementById('status-dot');
        const statusText = document.getElementById('status-text');
        const sizeInfo = document.getElementById('size-info');
        const paneBody = document.getElementById('input-pane-body');

        function isBase64(str) {
            if (str.length < 4) return false;
            const trimmed = str.trim();
            if (/\s/.test(trimmed) && !/^[A-Za-z0-9+/=\s]+$/.test(trimmed)) return false;
            const clean = trimmed.replace(/\s/g, '');
            if (!/^[A-Za-z0-9+/]*={0,2}$/.test(clean)) return false;
            if (clean.length % 4 !== 0) return false;
            try {
                atob(clean);
                return true;
            } catch (e) {
                return false;
            }
        }

        input.addEventListener('paste', () => {
            setTimeout(() => {
                const val = input.value.trim();
                if (val && isBase64(val)) {
                    setMode('decode');
                } else {
                    setMode('encode');
                }
                process();
            }, 10);
        });

        input.addEventListener('input', () => {
            process();
        });

        // Drag and drop
        paneBody.addEventListener('dragover', (e) => {
            e.preventDefault();
            paneBody.classList.add('drag-over');
        });

        paneBody.addEventListener('dragleave', () => {
            paneBody.classList.remove('drag-over');
        });

        paneBody.addEventListener('drop', (e) => {
            e.preventDefault();
            paneBody.classList.remove('drag-over');
            const file = e.dataTransfer.files[0];
            if (!file) return;

            const reader = new FileReader();
            reader.onload = () => {
                const dataUrl = reader.result;
                const b64 = dataUrl.split(',')[1] || '';
                setMode('encode');
                input.value = '[file: ' + file.name + ']';
                lastOutput = b64;
                outputEl.textContent = b64;
                outputTitle.classList.add('active');
                statusDot.className = 'status-dot valid';
                statusText.textContent = 'encoded file';

                const inBytes = file.size;
                const outBytes = new Blob([b64]).size;
                inputMeta.textContent = formatBytes(inBytes);
                outputMeta.textContent = formatBytes(outBytes);
                sizeInfo.textContent = formatBytes(inBytes) + ' \u2192 ' + formatBytes(outBytes);
            };
            reader.readAsDataURL(file);
        });

        function process() {
            const val = input.value;
            if (!val.trim()) {
                resetOutput();
                return;
            }

            try {
                let result;
                if (mode === 'encode') {
                    result = btoa(unescape(encodeURIComponent(val)));
                    statusDot.className = 'status-dot valid';
                    statusText.textContent = 'encoded';
                } else {
                    const clean = val.trim().replace(/\s/g, '');
                    result = decodeURIComponent(escape(atob(clean)));
                    statusDot.className = 'status-dot valid';
                    statusText.textContent = 'decoded';
                }

                lastOutput = result;
                outputEl.textContent = result;
                outputTitle.classList.add('active');

                const inBytes = new Blob([val]).size;
                const outBytes = new Blob([result]).size;
                inputMeta.textContent = formatBytes(inBytes);
                outputMeta.textContent = formatBytes(outBytes);
                sizeInfo.textContent = formatBytes(inBytes) + ' \u2192 ' + formatBytes(outBytes);
            } catch (e) {
                lastOutput = '';
                outputEl.innerHTML = '<div class="output-placeholder"><p style="color:var(--error)">Invalid input for ' + mode + '</p></div>';
                statusDot.className = 'status-dot';
                statusText.textContent = 'invalid input';
                outputMeta.textContent = '';
                sizeInfo.textContent = '';
            }
        }

        function setMode(m) {
            mode = m;
            document.getElementById('btn-encode').className = m === 'encode' ? 'btn btn-primary' : 'btn';
            document.getElementById('btn-decode').className = m === 'decode' ? 'btn btn-primary' : 'btn';
            document.querySelectorAll('.mode-btn').forEach(btn => {
                btn.classList.toggle('active', btn.dataset.mode === m);
            });
            process();
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
            lastOutput = '';
            resetOutput();
        }

        function resetOutput() {
            outputEl.innerHTML = '<div class="output-placeholder"><svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round"><path d="M8 3H5a2 2 0 0 0-2 2v3m18 0V5a2 2 0 0 0-2-2h-3m0 18h3a2 2 0 0 0 2-2v-3M3 16v3a2 2 0 0 0 2 2h3"/></svg><p>Paste text or drop a file to encode/decode</p></div>';
            outputTitle.classList.remove('active');
            statusDot.className = 'status-dot';
            statusText.textContent = 'ready';
            inputMeta.textContent = '';
            outputMeta.textContent = '';
            sizeInfo.textContent = '';
        }

        function formatBytes(bytes) {
            if (bytes < 1024) return bytes + ' B';
            return (bytes / 1024).toFixed(1) + ' KB';
        }
    </script>
@endsection
