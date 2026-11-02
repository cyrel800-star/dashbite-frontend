<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_root_redirects_to_the_shared_sign_in_modal(): void
    {
        $response = $this->get('/');

        $response->assertRedirect(route('customer.restaurants', ['login' => 1]));
    }
}
