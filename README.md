# DS Tools

A growing collection of developer utilities with a dark, minimal UI. Built with Laravel and vanilla JavaScript.

![License: MIT](https://img.shields.io/badge/license-MIT-blue.svg)

## Tools

### JSON Prettifier

Format, validate, and syntax-highlight JSON data.

- Pretty-print with configurable indentation (2 spaces, 4 spaces, or tabs)
- Color-coded syntax highlighting
- Minify to a single line
- Real-time validation with error position indicators
- Keyboard shortcuts (`⌘/Ctrl + Enter` to prettify)
- Copy formatted output to clipboard

### Request Inspector

A webhook/request catcher that captures incoming HTTP requests and displays them live.

- Accepts POST, PUT, and PATCH requests at `/webhook/{any-path}`
- Live feed updates via polling (1.5s interval)
- Syntax-highlighted JSON bodies
- Collapsible headers for each request
- Wildcard paths — `/webhook/stripe`, `/webhook/github/push`, etc.
- Copy the webhook URL with one click
- Clear all captured requests

### Regex Tester

Test regular expressions with real-time match highlighting and capture groups.

- Pattern input styled as `/pattern/flags` with toggle buttons for `g`, `i`, `m` flags
- Real-time match highlighting in the test string
- Match results with position ranges and capture group display
- Invalid pattern error reporting
- Collapsible quick reference for common regex syntax

### Base64

Encode and decode Base64 strings and files with auto-detection.

- Auto-detects Base64 input on paste and switches to decode mode
- Drag-and-drop file upload for encoding files to Base64
- Encode/decode toggle with live conversion
- Input/output byte size display in status bar

### JWT Debugger

Decode and inspect JWT tokens — header, payload, and expiry status.

- Paste a JWT to instantly decode header and payload
- Syntax-highlighted JSON output
- Algorithm badge (e.g. HS256, RS256)
- Claims summary with human-readable dates for `iat`/`exp`
- Expiration status badge (valid, expired, no exp claim)
- Signature not verified — decode only

### Speedtest

Measure network throughput and latency against this server — ideal for internal/LAN testing.

- Download, upload, ping, and jitter measured directly against this app (no third-party servers)
- Log-scale gauge with a live needle and digital readout (0–1000+ Mbps)
- Parallel connection streams for accurate high-bandwidth measurement
- Incompressible random payloads so results aren't skewed by compression
- Abort a test in progress at any time
- Endpoints: `GET /speedtest/download`, `POST /speedtest/upload`, `GET /speedtest/ping`

## Installation

```bash
# Clone the repository
git clone https://github.com/dark-social/ds-tools.git
cd ds-tools

# Install dependencies
composer install

# Set up environment
cp .env.example .env
php artisan key:generate

# Run migrations
php artisan migrate

# Start the development server
composer dev
```

### Production deployment

```bash
# Set ownership so your user and the web server group can both access files
sudo chown -R $USER:www-data storage bootstrap/cache

# Storage directories need to be writable by the web server
sudo chmod -R g+w storage bootstrap/cache

# SQLite needs write access to both the database file and its parent directory
# (SQLite creates a journal file alongside the database)
sudo chown $USER:www-data database/database.sqlite
sudo chmod g+w database/database.sqlite database/
```

## Usage

Visit the home page to pick a tool, or go directly:

- `/json-prettifier` — paste JSON, hit prettify
- `/request-inspector` — grab the webhook URL and start sending requests
- `/regex-tester` — enter a pattern and test string to see matches
- `/base64` — paste text or drop a file to encode/decode
- `/jwt-debugger` — paste a JWT to inspect header and payload
- `/speedtest` — hit start to measure download, upload, ping, and jitter

### Quick test for Request Inspector

```bash
curl -X POST http://localhost:8000/webhook/test \
  -H "Content-Type: application/json" \
  -d '{"hello": "world"}'
```

The request will appear on the inspector page within 1.5 seconds.

## Tech Stack

- **Backend**: Laravel 11+ / PHP 8.2+
- **Frontend**: Vanilla JavaScript (no frameworks)
- **Database**: SQLite
- **Styling**: Custom CSS with JetBrains Mono + Outfit fonts

## Development

```bash
# Run tests
composer test

# Run linter
./vendor/bin/pint
```

## License

MIT License — see the [LICENSE](LICENSE) file for details.
