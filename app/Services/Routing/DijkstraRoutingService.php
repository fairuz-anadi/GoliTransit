<?php

namespace App\Services\Routing;

use RuntimeException;

class DijkstraRoutingService
{
    public function run(array $graph, string $start, string $end, string $mode): array
    {
        if (! isset($graph[$start])) {
            throw new RuntimeException("Unknown start node [{$start}].");
        }

        if (! isset($graph[$end])) {
            throw new RuntimeException("Unknown destination node [{$end}].");
        }

        $distances = [];
        $previous = [];
        $visited = [];

        foreach ($graph as $node => $_edges) {
            $distances[$node] = INF;
            $previous[$node] = null;
            $visited[$node] = false;
        }

        $distances[$start] = 0;

        while (true) {
            $current = $this->getClosestUnvisitedNode($distances, $visited);

            if ($current === null) {
                break;
            }

            if ($current === $end) {
                break;
            }

            $visited[$current] = true;

            foreach ($graph[$current] as $edge) {
                if (! in_array($mode, $edge['modes'], true)) {
                    continue;
                }

                $neighbor = $edge['to'];
                $candidateDistance = $distances[$current] + $edge['cost'];

                if ($candidateDistance < $distances[$neighbor]) {
                    $distances[$neighbor] = $candidateDistance;
                    $previous[$neighbor] = [
                        'edge_id' => $edge['id'],
                        'node' => $current,
                        'cost' => $edge['cost'],
                    ];
                }
            }
        }

        if ($distances[$end] === INF) {
            throw new RuntimeException("No {$mode} route is available from {$start} to {$end}.");
        }

        return [
            'path' => $this->buildPath($previous, $start, $end),
            'segments' => $this->buildSegments($previous, $start, $end, $mode),
            'total_cost' => $distances[$end],
            'mode' => $mode,
        ];
    }

    protected function getClosestUnvisitedNode(array $distances, array $visited): ?string
    {
        $closestNode = null;
        $closestDistance = INF;

        foreach ($distances as $node => $distance) {
            if ($visited[$node]) {
                continue;
            }

            if ($distance < $closestDistance) {
                $closestDistance = $distance;
                $closestNode = $node;
            }
        }

        return $closestNode;
    }

    protected function buildPath(array $previous, string $start, string $end): array
    {
        $path = [];
        $cursor = $end;

        while ($cursor !== null) {
            array_unshift($path, $cursor);

            if ($cursor === $start) {
                break;
            }

            $cursor = $previous[$cursor]['node'] ?? null;
        }

        return $path;
    }

    protected function buildSegments(array $previous, string $start, string $end, string $mode): array
    {
        $segments = [];
        $cursor = $end;

        while ($cursor !== $start) {
            $segment = $previous[$cursor] ?? null;

            if ($segment === null) {
                break;
            }

            array_unshift($segments, [
                'edge_id' => $segment['edge_id'],
                'from' => $segment['node'],
                'to' => $cursor,
                'cost' => $segment['cost'],
                'mode' => $mode,
            ]);

            $cursor = $segment['node'];
        }

        return $segments;
    }
}
