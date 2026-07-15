<?php

namespace App\Http\Controllers;

use App\Services\NotifikasiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotifikasiController extends Controller
{
    public function dismiss(Request $request): JsonResponse
    {
        $data = $request->validate([
            'key' => 'required|string|max:120',
        ]);

        NotifikasiService::dismiss(auth()->id(), $data['key']);

        return response()->json([
            'success' => true,
            'count' => NotifikasiService::countForUser(auth()->user()),
        ]);
    }

    public function dismissAll(Request $request): JsonResponse
    {
        $keys = $request->input('keys', []);

        if (!is_array($keys) || empty($keys)) {
            $keys = NotifikasiService::getAllCurrentKeys(auth()->user());
        }

        NotifikasiService::dismissMany(auth()->id(), $keys);

        return response()->json([
            'success' => true,
            'count' => 0,
        ]);
    }
}
