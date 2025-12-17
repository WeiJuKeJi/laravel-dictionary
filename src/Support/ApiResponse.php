<?php

namespace WeiJuKeJi\LaravelDictionary\Support;

use Illuminate\Http\JsonResponse;

trait ApiResponse
{
    /**
     * 成功响应
     */
    protected function success($data = null, string $message = 'Success', int $code = 200): JsonResponse
    {
        return response()->json([
            'code' => $code,
            'message' => $message,
            'data' => $data,
        ], $code);
    }

    /**
     * 错误响应
     */
    protected function error(string $message = 'Error', int $code = 400, $errors = null): JsonResponse
    {
        $response = [
            'code' => $code,
            'message' => $message,
        ];

        if ($errors !== null) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $code);
    }
}
