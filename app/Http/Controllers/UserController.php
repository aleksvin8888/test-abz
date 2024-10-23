<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'count' => 'integer|min:1|max:100',
            'page' => 'integer|min:1',
            'offset' => 'integer|min:0|nullable',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'fails' => $validator->errors()
            ], 422);
        }
        $count = $request->query('count', 5);
        $page = $request->query('page', 1);
        $offset = $request->query('offset');

        $paginationResult = User::customPaginate($count, $page, $offset);

        return response()->json([
            'success' => true,
            'page' => $paginationResult['pagination']['page'],
            'total_pages' => $paginationResult['pagination']['total_pages'],
            'total_users' => $paginationResult['pagination']['total_users'],
            'count' => $paginationResult['pagination']['count'],
            'links' => $paginationResult['pagination']['links'],
            'users' => UserResource::collection($paginationResult['users']),
        ]);
    }

    public function show($id): JsonResponse
    {
        $validator = Validator::make(['user_id' => $id], [
            'user_id' => 'integer|min:1',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'fails' => $validator->errors(),
            ], 400);
        }

        $user = User::with('position')->find($id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'The user with the requested identifier does not exist',
                'fails' => [
                    'user_id' => ['User not found']
                ],
            ], 404);
        }

        return response()->json([
            'success' => true,
            'user' => UserResource::make($user)
        ]);
    }
}
