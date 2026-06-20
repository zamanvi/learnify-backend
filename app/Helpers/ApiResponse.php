<?php

namespace App\Helpers;

class ApiResponse
{
    /**
     * Return an API response based on status.
     *
     * @param bool $status
     * @param string $message
     * @param mixed $data
     * @param int $statusCode
     * @return \Illuminate\Http\JsonResponse
     */
    public static function respond($data, $status, $message, $statusCode)
    {
        $response = [
            'status' => $status,
            'code' => $statusCode,
            'message' => $message,
        ];
        if ($status) {
            $response['data'] = ($data != null) ? $data : '';
        }
        $responseKey = $status ? 'success' : 'error';
        return response()->json([$responseKey => $response], $statusCode);
    }
}
