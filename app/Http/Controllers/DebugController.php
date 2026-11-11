<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;

class DebugController extends Controller
{
    public function simple(): JsonResponse
    {
        return response()->json(['ok' => true]);
    }

    public function path(Request $request): JsonResponse
    {
        return response()->json([
            'uri' => $request->getRequestUri(),
            'path' => $request->path(),
            'isApiLogin' => $request->is('api/*'),
            'csrfExcepts' => PreventRequestForgery::class !== null,
        ]);
    }
}