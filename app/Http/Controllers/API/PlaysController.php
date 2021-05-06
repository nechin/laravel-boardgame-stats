<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\BGG\Contracts\BGG;
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
     * @param BGG $bgg
     * @return JsonResponse
     */
    public function get(string $userName, BGG $bgg): JsonResponse
    {
        try {
            return response()->json([
                'stats' => $bgg->getUserPlaysStat($userName)
            ]);
        } catch (Exception $exception) {
            return response()->json([
                'error' => $exception->getMessage()
            ], 401);
        }
    }
}
