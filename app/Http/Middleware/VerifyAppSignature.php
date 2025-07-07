<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Client;

class VerifyAppSignature
{
    public function handle(Request $request, Closure $next)
    {
        $appKey = $request->header('X-App-Key');

        $signature = $request->header('X-Signature');

        $timestamp = $request->header('timestamp');

        if (!$appKey || !$signature || !$timestamp) {
            return response()->json(['message' => 'Missing authentication headers'], 401);
        }

        $client = Client::where('app_key', $appKey)->first();

        if (!$client) {
            return response()->json(['message' => 'Invalid app key'], 401);
        }

        $body = $request->getContent(); // Raw body

        $expectedSignature = $this->generateSignature($signature, $body, $timestamp, $client->app_secret);

        if (!$expectedSignature) {
            return response()->json(['message' => 'Invalid signature', 'app_secret' => $client->app_secret, 'body' => $body, 'expectedSignature' => $expectedSignature], 401);
        }

        return $next($request);
    }

    function generateSignature(string $path, $body, int $timestamp, string $secret): string
    {
        // Chuẩn hóa path
        $normalizedPath = trim($path, '/');

        // Chuẩn hóa body
        if (is_null($body) || $body === '') {
            $bodyStr = '';
        } elseif (is_string($body)) {
            $bodyStr = $body;
        } elseif (is_array($body) || is_object($body)) {
            $bodyStr = json_encode($this->sortArrayKeys($body));
        } else {
            $bodyStr = (string)$body;
        }

        // Tạo message
        $message = "{$normalizedPath}|{$bodyStr}|{$timestamp}";

        error_log("Signing message: " . $message); // Log debug

        return hash_hmac('sha256', $message, $secret);
    }

    // Hàm sắp xếp keys của mảng
    public  function sortArrayKeys($array)
    {
        if (!is_array($array)) return $array;

        ksort($array);

        return array_map('sortArrayKeys', $array);
    }
}
