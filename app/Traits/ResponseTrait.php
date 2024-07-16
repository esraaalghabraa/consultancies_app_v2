<?php

namespace App\Traits;

trait ResponseTrait
{
    public function successResponse($data = null, $message = null, $statusCode = 200)
    {
        return response()->json([
            'data' => $data,
            'message' => $message
        ], $statusCode);
    }

    public function failedResponse($message = null, $statusCode = 500)
    {
        return response()->json([
            'data' => null,
            'message' => $message
        ], $statusCode);
    }
}
