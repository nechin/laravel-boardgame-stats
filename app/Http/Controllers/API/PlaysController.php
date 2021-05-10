<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\BGG\BggService;
use Exception;
use Illuminate\Http\JsonResponse;

/**
 * Class PlaysController
 * @package App\Http\Controllers\API
 */
class PlaysController extends Controller
{
    /**
     * @param string $userName
     * @return JsonResponse
     */
    public function get(string $userName): JsonResponse
    {
        try {
            $service = new BggService();
            return response()->json([
                'stats' => $service->getUserPlaysStat($userName)
            ]);
        } catch (Exception $exception) {
            return response()->json([
                'error' => $exception->getMessage()
            ], 401);
        }
    }
}
