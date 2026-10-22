<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'DS Tools') | DS Tools</title>
    <link rel="icon" type="image/svg+xml" href="/ds-purple.svg">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@300;400;500;600;700&family=Outfit:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-deep: #0a0a0c;
            --bg-panel: #111115;
            --bg-elevated: #18181e;
            --bg-hover: #1e1e26;
            --border: #2a2a35;
            --border-focus: #4a4a5a;
            --text-primary: #e8e8ed;
            --text-secondary: #8888a0;
            --text-dim: #555568;
            --accent: #574099;
            --accent-dim: rgba(87, 64, 153, 0.12);
            --error: #ff4d6a;
            --error-dim: rgba(255, 77, 106, 0.08);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'JetBrains Mono', monospace;
            background: var(--bg-deep);
            color: var(--text-primary);
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* Subtle grid background */
        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background-image:
                linear-gradient(var(--border) 1px, transparent 1px),
                linear-gradient(90deg, var(--border) 1px, transparent 1px);
            background-size: 60px 60px;
            opacity: 0.08;
            pointer-events: none;
            z-index: 0;
        }

        .app {
            position: relative;
            z-index: 1;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Header / Nav */
        header {
            border-bottom: 1px solid var(--border);
            padding: 1.25rem 2rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            backdrop-filter: blur(12px);
            background: rgba(10, 10, 12, 0.8);
            position: sticky;
            top: 0;
            z-index: 10;
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 2rem;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            text-decoration: none;
            color: inherit;
        }

        .logo-mark {
            width: 24px;
            height: 24px;
            flex-shrink: 0;
        }

        .logo-mark img {
            width: 100%;
            height: 100%;
        }

        .logo-text {
            font-family: 'Outfit', sans-serif;
            font-weight: 700;
            font-size: 1.1rem;
            letter-spacing: -0.5px;
        }

        .logo-text span {
            color: var(--accent);
        }

        /* Navigation */
        nav {
            display: flex;
            align-items: center;
            gap: 0.25rem;
        }

        nav a {
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.72rem;
            font-weight: 500;
            padding: 0.4rem 0.75rem;
            color: var(--text-secondary);
            text-decoration: none;
            border: 1px solid transparent;
            transition: all 0.15s ease;
            letter-spacing: 0.02em;
        }

        nav a:hover {
            color: var(--text-primary);
            background: var(--bg-hover);
            border-color: var(--border);
        }

        nav a.active {
            color: var(--accent);
            background: var(--accent-dim);
            border-color: rgba(87, 64, 153, 0.3);
        }

        .header-actions {
            display: flex;
            gap: 0.5rem;
        }

        /* Buttons */
        .btn {
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.72rem;
            font-weight: 500;
            padding: 0.5rem 1rem;
            border: 1px solid var(--border);
            background: var(--bg-elevated);
            color: var(--text-secondary);
            cursor: pointer;
            transition: all 0.15s ease;
            letter-spacing: 0.02em;
            display: flex;
            align-items: center;
            gap: 0.4rem;
        }

        .btn:hover {
            background: var(--bg-hover);
            color: var(--text-primary);
            border-color: var(--border-focus);
        }

        .btn-primary {
            background: var(--accent);
            color: #fff;
            border-color: var(--accent);
            font-weight: 600;
        }

        .btn-primary:hover {
            background: #6b50b5;
            border-color: #6b50b5;
            color: #fff;
            box-shadow: 0 0 20px rgba(87, 64, 153, 0.3);
        }

        .btn-danger {
            color: var(--error);
            border-color: rgba(255, 77, 106, 0.2);
        }

        .btn-danger:hover {
            background: var(--error-dim);
            border-color: var(--error);
        }

        .btn kbd {
            font-family: inherit;
            font-size: 0.65rem;
            padding: 0.1rem 0.35rem;
            background: rgba(255, 255, 255, 0.06);
            border: 1px solid rgba(255, 255, 255, 0.08);
            color: var(--text-dim);
        }

        /* Scrollbar styling */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        ::-webkit-scrollbar-track {
            background: transparent;
        }
        ::-webkit-scrollbar-thumb {
            background: var(--border);
            border-radius: 3px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: var(--border-focus);
        }

        /* Responsive nav */
        @media (max-width: 900px) {
            header {
                flex-wrap: wrap;
                gap: 0.75rem;
                padding: 1rem 1.25rem;
            }
            .header-left {
                gap: 1rem;
            }
            nav {
                order: 3;
                width: 100%;
                overflow-x: auto;
            }
        }

        @yield('styles')
    </style>
</head>
<body>
    <div class="app">
        <header>
            <div class="header-left">
                <a href="/" class="logo">
                    <div class="logo-mark"><img src="/ds-purple.svg" alt="DS"></div>
                    <div class="logo-text"><span>ds</span>tools</div>
                </a>
                <nav>
                    <a href="{{ route('tools.json-prettifier') }}" class="{{ request()->routeIs('tools.json-prettifier') ? 'active' : '' }}">json prettifier</a>
                    <a href="{{ route('tools.minifier') }}" class="{{ request()->routeIs('tools.minifier') ? 'active' : '' }}">minifier</a>
                    <a href="{{ route('tools.html-formatter') }}" class="{{ request()->routeIs('tools.html-formatter') ? 'active' : '' }}">html formatter</a>
                    <a href="{{ route('tools.request-inspector') }}" class="{{ request()->routeIs('tools.request-inspector') ? 'active' : '' }}">request inspector</a>
                    <a href="{{ route('tools.regex-tester') }}" class="{{ request()->routeIs('tools.regex-tester') ? 'active' : '' }}">regex tester</a>
                    <a href="{{ route('tools.base64') }}" class="{{ request()->routeIs('tools.base64') ? 'active' : '' }}">base64</a>
                    <a href="{{ route('tools.jwt-debugger') }}" class="{{ request()->routeIs('tools.jwt-debugger') ? 'active' : '' }}">jwt debugger</a>
                    <a href="{{ route('tools.speedtest') }}" class="{{ request()->routeIs('tools.speedtest') ? 'active' : '' }}">speedtest</a>
                </nav>
            </div>
            <div class="header-actions">
                @yield('actions')
            </div>
        </header>

        @yield('content')
    </div>

    @yield('scripts')
</body>
</html>
