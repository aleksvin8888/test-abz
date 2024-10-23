<?php

namespace App\Http\Controllers;

use App\Http\Resources\PositionResource;
use App\Models\Position;
use Illuminate\Http\JsonResponse;

class PositionController extends Controller
{
    public function index(): JsonResponse
    {
       $positions = Position::all();

        if ($positions->isEmpty()) {
            return response()->json([
                'success' => false,
                "message" => "Positions not found"
            ], 404);
        }

        return response()->json([
            'success' => true,
            'positions' => PositionResource::collection($positions)
        ]);
    }
}

