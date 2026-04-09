<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Routing\DemoGraphService;
use App\Services\Routing\DijkstraRoutingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use RuntimeException;

class RouteController extends Controller
{
    public function __invoke(
        Request $request,
        DemoGraphService $graphService,
        DijkstraRoutingService $routingService
    ): JsonResponse {
        $graph = $graphService->getGraph();
        $nodes = $graphService->getNodes();

        $validated = $request->validate([
            'start' => ['required', 'string', Rule::in($nodes)],
            'destination' => ['required', 'string', Rule::in($nodes)],
            'allowed_modes' => ['required', 'array', 'min:1'],
            'allowed_modes.*' => ['required', 'string', Rule::in(['car', 'rickshaw', 'walk'])],
        ]);

        $mode = $validated['allowed_modes'][0];

        try {
            $route = $routingService->run(
                $graph,
                $validated['start'],
                $validated['destination'],
                $mode
            );
        } catch (RuntimeException $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], 422);
        }

        return response()->json([
            'data' => [
                'start' => $validated['start'],
                'destination' => $validated['destination'],
                'allowed_modes' => $validated['allowed_modes'],
                'selected_mode' => $mode,
                'path' => $route['path'],
                'segments' => $route['segments'],
                'total_cost' => $route['total_cost'],
                'justification' => [
                    'summary' => "Best available {$mode} route on the current demo graph.",
                    'mode_switches' => 0,
                    'mode_switch_penalty_applied' => 0,
                    'note' => 'This is the Step A2 single-mode routing baseline. Multi-modal switching comes next.',
                ],
            ],
        ]);
    }
}
