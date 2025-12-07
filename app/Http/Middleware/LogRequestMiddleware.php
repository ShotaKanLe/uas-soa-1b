<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Context; // Fitur baru Laravel 11 untuk Global Context
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class LogRequestMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Generate Trace ID Unik
        $traceId = (string) Str::uuid();

        // 2. Set Context Global
        // Semua Log::info() di aplikasi otomatis akan menempelkan data ini
        Context::add('trace_id', $traceId);
        Context::add('user_ip', $request->ip());
        Context::add('url', $request->fullUrl());
        Context::add('method', $request->method());

        // Log::shareContext agar kompatibel dengan driver logging tertentu
        Log::shareContext([
            'trace_id' => $traceId,
            'user_id' => $request->user() ? $request->user()->id : 'guest',
        ]);

        // 3. Log Request Masuk (Awal Tracing)
        Log::info('Incoming Request', [
            'payload' => $request->except(['password', 'password_confirmation']), // Jangan log password!
        ]);

        // Teruskan request ke Controller
        $response = $next($request);

        // 4. Log Response Keluar (Akhir Tracing)
        Log::info('Outgoing Response', [
            'status' => $response->getStatusCode(),
        ]);

        // 5. Lampirkan Trace ID ke Header Response (Untuk debugging di Postman/Frontend)
        $response->headers->set('X-Trace-ID', $traceId);

        return $response;
    }
}
