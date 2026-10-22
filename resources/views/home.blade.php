@extends('layouts.app')

@section('title', 'Web Toolkit')

@section('styles')
    .home-content {
        flex: 1;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 4rem 2rem;
        gap: 3rem;
    }

    .home-hero {
        text-align: center;
        max-width: 500px;
    }

    .home-hero-logo {
        width: 64px;
        height: 64px;
        margin: 0 auto 1rem;
    }

    .home-hero h1 {
        font-family: 'Outfit', sans-serif;
        font-size: 2.5rem;
        font-weight: 800;
        letter-spacing: -1.5px;
        margin-bottom: 0.75rem;
    }

    .home-hero h1 span {
        color: var(--accent);
    }

    .home-hero p {
        font-size: 0.85rem;
        color: var(--text-secondary);
        line-height: 1.7;
    }

    .tools-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
        gap: 1rem;
        width: 100%;
        max-width: 700px;
    }

    .tool-card {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
        padding: 1.5rem;
        background: var(--bg-panel);
        border: 1px solid var(--border);
        text-decoration: none;
        color: inherit;
        transition: all 0.15s ease;
    }

    .tool-card:hover {
        background: var(--bg-elevated);
        border-color: var(--border-focus);
        transform: translateY(-1px);
    }

    .tool-card-icon {
        font-size: 1.25rem;
        color: var(--accent);
        margin-bottom: 0.25rem;
    }

    .tool-card-name {
        font-family: 'Outfit', sans-serif;
        font-weight: 600;
        font-size: 1rem;
        letter-spacing: -0.3px;
    }

    .tool-card-desc {
        font-size: 0.72rem;
        color: var(--text-secondary);
        line-height: 1.6;
    }
@endsection

@section('content')
    <div class="home-content">
        <div class="home-hero">
            <img src="/ds-purple.svg" alt="DS" class="home-hero-logo">
            <h1><span>ds</span>tools</h1>
            <p>A collection of developer utilities.<br>Pick a tool to get started.</p>
        </div>

        <div class="tools-grid">
            <a href="{{ route('tools.json-prettifier') }}" class="tool-card">
                <div class="tool-card-icon">{ }</div>
                <div class="tool-card-name">JSON Prettifier</div>
                <div class="tool-card-desc">Format, validate, and syntax-highlight JSON with configurable indentation.</div>
            </a>

            <a href="{{ route('tools.minifier') }}" class="tool-card">
                <div class="tool-card-icon">{ }</div>
                <div class="tool-card-name">Minifier</div>
                <div class="tool-card-desc">Minify and beautify JavaScript and CSS with syntax-highlighted output.</div>
            </a>

            <a href="{{ route('tools.html-formatter') }}" class="tool-card">
                <div class="tool-card-icon">&lt;/&gt;</div>
                <div class="tool-card-name">HTML Formatter</div>
                <div class="tool-card-desc">Format, beautify, and minify HTML with configurable indentation.</div>
            </a>

            <a href="{{ route('tools.request-inspector') }}" class="tool-card">
                <div class="tool-card-icon">&lt;/&gt;</div>
                <div class="tool-card-name">Request Inspector</div>
                <div class="tool-card-desc">Catch and inspect incoming webhooks and HTTP requests in real time.</div>
            </a>

            <a href="{{ route('tools.regex-tester') }}" class="tool-card">
                <div class="tool-card-icon">.*</div>
                <div class="tool-card-name">Regex Tester</div>
                <div class="tool-card-desc">Test regular expressions with real-time match highlighting and capture groups.</div>
            </a>

            <a href="{{ route('tools.base64') }}" class="tool-card">
                <div class="tool-card-icon">64</div>
                <div class="tool-card-name">Base64</div>
                <div class="tool-card-desc">Encode and decode Base64 strings and files with auto-detection.</div>
            </a>

            <a href="{{ route('tools.jwt-debugger') }}" class="tool-card">
                <div class="tool-card-icon">JWT</div>
                <div class="tool-card-name">JWT Debugger</div>
                <div class="tool-card-desc">Decode and inspect JWT tokens. View header, payload, and expiry status.</div>
            </a>

            <a href="{{ route('tools.speedtest') }}" class="tool-card">
                <div class="tool-card-icon">&#8645;</div>
                <div class="tool-card-name">Speedtest</div>
                <div class="tool-card-desc">Measure download, upload, ping, and jitter against this server &mdash; ideal for internal networks.</div>
            </a>

        </div>
    </div>
@endsection
