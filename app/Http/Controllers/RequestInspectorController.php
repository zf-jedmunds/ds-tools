<?php

namespace App\Http\Controllers;

use App\Models\WebhookRequest;
use Illuminate\Http\Request;

class RequestInspectorController extends Controller
{
    public function index()
    {
        $requests = WebhookRequest::orderByDesc('received_at')->limit(50)->get();

        return view('request-inspector', [
            'requests' => $requests,
        ]);
    }

    public function receive(Request $request, ?string $path = null)
    {
        WebhookRequest::create([
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'headers' => $request->headers->all(),
            'body' => $request->getContent(),
            'query_string' => $request->getQueryString(),
            'content_type' => $request->header('Content-Type'),
            'ip_address' => $request->ip(),
            'received_at' => now(),
        ]);

        return response()->json(['status' => 'ok'], 200);
    }

    public function poll(Request $request)
    {
        $afterId = $request->query('after_id');

        $query = WebhookRequest::orderByDesc('id');

        if ($afterId) {
            $query->where('id', '>', $afterId);
        }

        $requests = $query->limit(50)->get();

        return response()->json([
            'requests' => $requests,
            'last_id' => $requests->first()?->id,
        ]);
    }

    public function clear()
    {
        WebhookRequest::truncate();

        return response()->json(['status' => 'cleared']);
    }
}
