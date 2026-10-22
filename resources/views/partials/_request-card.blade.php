<div class="request-header">
    <span class="method-badge {{ strtolower($req->method) }}">{{ $req->method }}</span>
    <span class="request-url">{{ $req->url }}</span>
    <span class="request-time">{{ $req->received_at->format('H:i:s') }}</span>
</div>

@if($req->headers)
    <div class="request-section">
        <button class="section-toggle" onclick="toggleSection(this, 'headers-{{ $req->id }}')">
            <span class="arrow">&#9654;</span> headers
        </button>
        <div class="section-content" id="headers-{{ $req->id }}">
            <table class="headers-table">
                @foreach($req->headers as $key => $values)
                    <tr>
                        <td>{{ $key }}</td>
                        <td>{{ is_array($values) ? implode(', ', $values) : $values }}</td>
                    </tr>
                @endforeach
            </table>
        </div>
    </div>
@endif

@if($req->body && trim($req->body))
    @php
        $bodyDisplay = $req->body;
        $isJson = false;
        try {
            $parsed = json_decode($req->body, true, 512, JSON_THROW_ON_ERROR);
            $bodyDisplay = json_encode($parsed, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
            $isJson = true;
        } catch (\JsonException $e) {}
    @endphp
    <div class="request-section">
        <button class="section-toggle open" onclick="toggleSection(this, 'body-{{ $req->id }}')">
            <span class="arrow">&#9654;</span> body
        </button>
        <div class="section-content show" id="body-{{ $req->id }}">
            <div class="json-block" @if($isJson) data-json-highlight @endif>{{ $bodyDisplay }}</div>
        </div>
    </div>
@endif
