<?php

namespace App\Services\Routing;

class DemoGraphService
{
    /**
     * Return a small hardcoded graph so we can validate the routing engine
     * before plugging in the real city graph.
     */
    public function getGraph(): array
    {
        return [
            'farmgate' => [
                [
                    'id' => 'edge_farmgate_karwan_bazar',
                    'to' => 'karwan_bazar',
                    'cost' => 4,
                    'modes' => ['car', 'rickshaw', 'walk'],
                ],
                [
                    'id' => 'edge_farmgate_panthapath',
                    'to' => 'panthapath',
                    'cost' => 2,
                    'modes' => ['walk', 'rickshaw'],
                ],
                [
                    'id' => 'edge_farmgate_green_road',
                    'to' => 'green_road',
                    'cost' => 2,
                    'modes' => ['walk', 'rickshaw'],
                ],
            ],
            'karwan_bazar' => [
                [
                    'id' => 'edge_karwan_bazar_farmgate',
                    'to' => 'farmgate',
                    'cost' => 4,
                    'modes' => ['car', 'rickshaw', 'walk'],
                ],
                [
                    'id' => 'edge_karwan_bazar_tejgaon',
                    'to' => 'tejgaon',
                    'cost' => 3,
                    'modes' => ['car', 'rickshaw'],
                ],
                [
                    'id' => 'edge_karwan_bazar_green_road',
                    'to' => 'green_road',
                    'cost' => 8,
                    'modes' => ['car', 'rickshaw', 'walk'],
                ],
            ],
            'panthapath' => [
                [
                    'id' => 'edge_panthapath_farmgate',
                    'to' => 'farmgate',
                    'cost' => 2,
                    'modes' => ['walk', 'rickshaw'],
                ],
                [
                    'id' => 'edge_panthapath_green_road',
                    'to' => 'green_road',
                    'cost' => 2,
                    'modes' => ['walk', 'rickshaw'],
                ],
            ],
            'green_road' => [
                [
                    'id' => 'edge_green_road_farmgate',
                    'to' => 'farmgate',
                    'cost' => 2,
                    'modes' => ['walk', 'rickshaw'],
                ],
                [
                    'id' => 'edge_green_road_panthapath',
                    'to' => 'panthapath',
                    'cost' => 2,
                    'modes' => ['walk', 'rickshaw'],
                ],
                [
                    'id' => 'edge_green_road_karwan_bazar',
                    'to' => 'karwan_bazar',
                    'cost' => 8,
                    'modes' => ['car', 'rickshaw', 'walk'],
                ],
                [
                    'id' => 'edge_green_road_gulshan',
                    'to' => 'gulshan',
                    'cost' => 7,
                    'modes' => ['car', 'rickshaw'],
                ],
            ],
            'tejgaon' => [
                [
                    'id' => 'edge_tejgaon_karwan_bazar',
                    'to' => 'karwan_bazar',
                    'cost' => 3,
                    'modes' => ['car', 'rickshaw'],
                ],
                [
                    'id' => 'edge_tejgaon_gulshan',
                    'to' => 'gulshan',
                    'cost' => 5,
                    'modes' => ['car', 'rickshaw'],
                ],
            ],
            'gulshan' => [
                [
                    'id' => 'edge_gulshan_tejgaon',
                    'to' => 'tejgaon',
                    'cost' => 5,
                    'modes' => ['car', 'rickshaw'],
                ],
                [
                    'id' => 'edge_gulshan_green_road',
                    'to' => 'green_road',
                    'cost' => 7,
                    'modes' => ['car', 'rickshaw'],
                ],
            ],
        ];
    }

    public function getNodes(): array
    {
        return array_keys($this->getGraph());
    }

    public function getEdges(): array
    {
        $edges = [];

        foreach ($this->getGraph() as $from => $neighbors) {
            foreach ($neighbors as $edge) {
                $edges[] = [
                    'id' => $edge['id'],
                    'from' => $from,
                    'to' => $edge['to'],
                    'cost' => $edge['cost'],
                    'modes' => $edge['modes'],
                ];
            }
        }

        return $edges;
    }
}
