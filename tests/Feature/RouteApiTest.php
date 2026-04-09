<?php

namespace Tests\Feature;

use Tests\TestCase;

class RouteApiTest extends TestCase
{
    public function test_it_returns_a_single_mode_route(): void
    {
        $response = $this->postJson('/api/route', [
            'start' => 'farmgate',
            'destination' => 'gulshan',
            'allowed_modes' => ['car'],
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('data.selected_mode', 'car')
            ->assertJsonPath('data.path.0', 'farmgate')
            ->assertJsonPath('data.path.3', 'gulshan')
            ->assertJsonPath('data.total_cost', 12)
            ->assertJsonCount(3, 'data.segments');
    }

    public function test_it_refuses_to_use_edges_that_do_not_allow_the_mode(): void
    {
        $response = $this->postJson('/api/route', [
            'start' => 'farmgate',
            'destination' => 'green_road',
            'allowed_modes' => ['car'],
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('data.path', [
                'farmgate',
                'karwan_bazar',
                'green_road',
            ])
            ->assertJsonPath('data.total_cost', 12);
    }

    public function test_it_requires_valid_payload_fields(): void
    {
        $response = $this->postJson('/api/route', [
            'start' => 'farmgate',
        ]);

        $response->assertStatus(422)->assertJsonValidationErrors([
            'destination',
            'allowed_modes',
        ]);
    }
}
