<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SpeedtestController extends Controller
{
    /**
     * One megabyte of random, incompressible data generated per request-cycle
     * and repeated to fill the requested payload. Repeating a single random
     * block keeps CPU cost flat while staying incompressible on the wire.
     */
    private const BLOCK = 1048576; // 1 MiB

    /** Hard cap on a single download payload so the endpoint can't be abused. */
    private const MAX_DOWNLOAD = 1073741824; // 1 GiB

    public function index()
    {
        return view('speedtest');
    }

    /**
     * Streams `bytes` of incompressible garbage for download throughput tests.
     */
    public function download(Request $request): StreamedResponse
    {
        $bytes = (int) $request->query('bytes', 26214400); // 25 MiB default
        $bytes = max(self::BLOCK, min($bytes, self::MAX_DOWNLOAD));

        $response = new StreamedResponse(function () use ($bytes) {
            $block = random_bytes(self::BLOCK);
            $remaining = $bytes;

            while ($remaining > 0) {
                $chunk = $remaining >= self::BLOCK ? $block : substr($block, 0, $remaining);
                echo $chunk;
                $remaining -= strlen($chunk);

                if (ob_get_level() > 0) {
                    ob_flush();
                }
                flush();

                if (connection_aborted()) {
                    break;
                }
            }
        });

        $response->headers->set('Content-Type', 'application/octet-stream');
        $response->headers->set('Content-Length', (string) $bytes);
        $response->headers->set('Content-Encoding', 'identity');
        $response->headers->set('Content-Disposition', 'attachment; filename="speedtest.bin"');
        $this->noCache($response->headers);

        return $response;
    }

    /**
     * Drains the request body and reports how many bytes were received.
     * Used for upload throughput; body is read and discarded.
     */
    public function upload(Request $request)
    {
        $stream = fopen('php://input', 'rb');
        $total = 0;

        if ($stream) {
            while (! feof($stream)) {
                $data = fread($stream, self::BLOCK);
                if ($data === false) {
                    break;
                }
                $total += strlen($data);
            }
            fclose($stream);
        }

        // Fall back to the parsed content length when php://input is empty
        // (e.g. body already consumed by the framework).
        if ($total === 0) {
            $total = strlen($request->getContent());
        }

        $response = response()->json(['bytes' => $total]);
        $this->noCache($response->headers);

        return $response;
    }

    /**
     * Tiny no-cache response for latency / jitter measurement.
     */
    public function ping(Request $request)
    {
        $response = response('', 200)->header('Content-Type', 'text/plain');
        $this->noCache($response->headers);

        return $response;
    }

    private function noCache($headers): void
    {
        $headers->set('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
        $headers->set('Pragma', 'no-cache');
        $headers->set('Expires', '0');
    }
}
