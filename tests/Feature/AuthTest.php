<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AuthTest extends TestCase
{
    /**
     * Testing authentication for the endpoint that returns products
     */
    public function test_product_list_can_be_retrieved()
    {
        Sanctum::actingAs(
            User::factory()->create()
        );
        $response = $this->get('/api/products');

        $response->assertOk();
    }

    /**
     * Testing log in to the system with some credentials
     */
    public function test_log_in_to_system()
    {
        $user             = User::factory()->create();
        $response_correct = $this->call('POST', '/api/login', [
            'email'    => $user->email,
            'password' => $user->email
        ]);

        $response_failed = $this->call('POST', '/api/login', [
            'email'    => $user->email,
            'password' => $user->email.'-test'
        ]);

        $this->assertEquals(200, $response_correct->getStatusCode());
        $this->assertEquals(401, $response_failed->getStatusCode());
    }

}
