<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * Unauthenticated visitors now see the landing page at /.
     */
    public function test_the_application_returns_landing_page_for_guests(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('DuitPelajar');
        $response->assertSee('Kelola Uangmu');
    }
}
