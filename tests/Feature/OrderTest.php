<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class OrderTest extends TestCase
{
    /**
     * testing creating an order.
     *
     * @return void
     */
    public function test_create_an_order()
    {
        Sanctum::actingAs(
            User::factory()->create()
        );
        $response = $this->call('POST', '/api/order', [
            'address'  => [
                'first_name' => 'test',
                'last_name'  => 'test',
                'address'    => 'test',
                'phone'      => '123456',
                'email'      => 'test@test.com',
                'country'    => 'test',
                'city'       => 'test',
                'zipcode'    => '12345',
            ],
            'products' => [
                [
                    'product_id' => 1,
                    'quantity'   => 1,
                ]
            ]
        ]);

        $this->assertEquals(201, $response->getStatusCode());
        $response->assertJsonStructure([
            'id',
            'customer_id',
            'products' => [
                [
                    'order_id',
                    'product_id',
                    'quantity',
                ]
            ]
        ]);
    }

    /**
     * testing validation rules when creating an order.
     *
     * @return void
     */
    public function test_validation_by_create_an_order()
    {
        Sanctum::actingAs(
            User::factory()->create()
        );
        $response = $this->call('POST', '/api/order', [
            'address' => [
                'address' => 'test',
                'phone'   => '123456',
                'email'   => 'test@test.com',
                'country' => 'test',
                'city'    => 'test',
                'zipcode' => '12345',
            ]
        ]);

        $this->assertEquals(400, $response->getStatusCode());
    }

    /**
     * testing authentication when creating an order.
     *
     * @return void
     */
    public function test_token_by_create_an_order()
    {
        $response = $this->call('POST', '/api/order', [
            'address'  => [
                'first_name' => 'test',
                'last_name'  => 'test',
                'address'    => 'test',
                'phone'      => '123456',
                'email'      => 'test@test.com',
                'country'    => 'test',
                'city'       => 'test',
                'zipcode'    => '12345',
            ],
            'products' => [
                [
                    'product_id' => 1,
                    'quantity'   => 1,
                ]
            ]
        ]);

        $this->assertEquals(403, $response->getStatusCode());
    }
}
