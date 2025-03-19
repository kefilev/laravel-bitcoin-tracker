<?php

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;

class BaseApiResponse
{
    /**
     * Success Response
     */
    public static function success(int $status = 200): JsonResponse
    {
        return response()->json(['success' => true], $status);
    }

    /**
     * Error Response
     */
    public static function error(int $status = 500): JsonResponse
    {
        return response()->json(['success' => false], $status);
    }
}
