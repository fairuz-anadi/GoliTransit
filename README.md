# GoliTransit

GoliTransit is a hackathon backend for multi-modal routing in dense Dhaka-style traffic conditions. This repo uses Laravel on Vercel, but the team blueprint still maps cleanly:

- Member A: routing engine and `POST /api/route`
- Member B: graph data and graph manager contract
- Member C: anomaly flow, graph snapshot, error handling, and frontend/demo wiring

## Current status

Done now:

- project skeleton is ready
- `GET /health` exists
- `POST /api/route` works against a demo graph
- graph edges now use stable `edge_id` values
- `GET /api/graph/snapshot` exists as a contract/debug endpoint
- `POST /api/anomaly` exists as a placeholder contract endpoint for Member C

Not done yet:

- real Dhaka graph data
- multi-modal switching with penalties
- live anomaly updates to graph weights
- active-session rerouting
- demo frontend visualization

## Team contract

These contracts are now frozen unless the whole team agrees to change them.

### Allowed modes

```json
["car", "rickshaw", "walk"]
```

### Node format

```json
{
  "id": "farmgate"
}
```

### Edge format

Every directed edge must use this shape:

```json
{
  "id": "edge_farmgate_karwan_bazar",
  "to": "karwan_bazar",
  "cost": 4,
  "modes": ["car", "rickshaw", "walk"]
}
```

### Graph format

Member B should build the real graph in this exact adjacency-list shape:

```json
{
  "farmgate": [
    {
      "id": "edge_farmgate_karwan_bazar",
      "to": "karwan_bazar",
      "cost": 4,
      "modes": ["car", "rickshaw", "walk"]
    }
  ],
  "karwan_bazar": []
}
```

## API contract

### `GET /health`

Response:

```json
{
  "status": "ok"
}
```

### `POST /api/route`

Purpose:
Return the best currently available route for one selected mode. Right now this is the Step A2 single-mode baseline.

Request body:

```json
{
  "start": "farmgate",
  "destination": "gulshan",
  "allowed_modes": ["car"]
}
```

Current behavior:

- validation requires valid node IDs
- the first mode in `allowed_modes` is used as the selected mode
- edges that do not support that mode are ignored

Response shape:

```json
{
  "data": {
    "start": "farmgate",
    "destination": "gulshan",
    "allowed_modes": ["car"],
    "selected_mode": "car",
    "path": ["farmgate", "karwan_bazar", "tejgaon", "gulshan"],
    "segments": [
      {
        "edge_id": "edge_farmgate_karwan_bazar",
        "from": "farmgate",
        "to": "karwan_bazar",
        "cost": 4,
        "mode": "car"
      }
    ],
    "total_cost": 12,
    "justification": {
      "summary": "Best available car route on the current demo graph.",
      "mode_switches": 0,
      "mode_switch_penalty_applied": 0,
      "note": "This is the Step A2 single-mode routing baseline. Multi-modal switching comes next."
    }
  }
}
```

### `GET /api/graph/snapshot`

Purpose:
Give Member B and Member C a stable debug endpoint that shows the current graph contract and edge IDs.

Response shape:

```json
{
  "data": {
    "nodes": ["farmgate", "karwan_bazar"],
    "edges": [
      {
        "id": "edge_farmgate_karwan_bazar",
        "from": "farmgate",
        "to": "karwan_bazar",
        "cost": 4,
        "modes": ["car", "rickshaw", "walk"]
      }
    ]
  }
}
```

### `POST /api/anomaly`

Purpose:
This is the future Step C3 endpoint. The placeholder is already added so Member C can start from a fixed request contract.

Request body:

```json
{
  "edge_ids": ["edge_tejgaon_gulshan"],
  "multiplier": 10
}
```

Target behavior later:

- inflate the specified edge costs
- reroute affected active sessions
- return affected edges, new weights, and rerouted-session count

Current placeholder behavior:

- validates the payload
- returns `501 Not Implemented`
- echoes the agreed contract back in JSON

## Ownership

### Member A

- routing logic
- route response contract
- future multi-modal switch logic
- future session rerouting logic

Files currently relevant:

- `app/Services/Routing/DemoGraphService.php`
- `app/Services/Routing/DijkstraRoutingService.php`
- `app/Http/Controllers/Api/RouteController.php`
- `routes/api.php`

### Member B

- replace demo graph with real Dhaka graph
- keep node IDs and edge shape exactly as documented
- later add anomaly-aware graph update methods

Best starting point:

- `app/Services/Routing/DemoGraphService.php`

### Member C

- implement real anomaly handling behind `POST /api/anomaly`
- extend `GET /api/graph/snapshot`
- add error handling and demo-facing integration

Best starting points:

- `app/Http/Controllers/Api/AnomalyController.php`
- `app/Http/Controllers/Api/GraphSnapshotController.php`

## Local commands

Install:

```bash
composer install
npm install
```

Serve locally:

```bash
php artisan serve
```

Useful checks:

```bash
php artisan route:list
```

## Deployment notes

Vercel files already exist:

- `api/index.php`
- `vercel.json`

Deploy is not the current blocker. Finish the graph contract and feature slices first, then deploy once `GET /health` and the route flow are stable.
