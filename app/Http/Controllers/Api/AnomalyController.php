<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AnomalyController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'edge_ids' => ['required', 'array', 'min:1'],
            'edge_ids.*' => ['required', 'string'],
            'multiplier' => ['required', 'numeric', 'min:1'],
        ]);

        return response()->json([
            'message' => 'Anomaly processing is reserved for Step C3.',
            'contract' => [
                'edge_ids' => $validated['edge_ids'],
                'multiplier' => $validated['multiplier'],
            ],
            'next_owner' => 'Member C',
            'expected_flow' => [
                'update graph weights for the supplied edge IDs',
                'reroute affected sessions',
                'return affected edges and reroute counts',
            ],
        ], 501);
    }
}
