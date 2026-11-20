<?php

namespace Tests\Feature;

use App\Models\Package;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        Package::create([
            'name' => 'Kothak Solo Express',
            'description' => 'Sesi photobox 10 menit',
            'duration' => 10,
            'max_participants' => 2,
            'price' => 25000,
            'is_active' => true,
        ]);

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('KOTHaK');
    }
}
