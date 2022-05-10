<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PromoCodeTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_all_promo_codes()
    {
        $response = $this->json('GET','api/promo-code');

        $response->dump();

        $response->assertStatus(200);
    }
    
    public function test_get_active_promo_codes()
    {
        $response = $this->getJson('api/promo-code?active');

        $response->assertStatus(200);
    }

    public function test_deactivate_promo_code()
    {
        $response = $this->putJson('api/promo-code/1');

        $response->assertStatus(200);
    }

   
    public function test_create_promo_code()
    {
        $response = $this->postJson('api/promo-code', [
            'promo_code' => 'safeboda',
            'max_rides' => 4,
            'radius' => 300,
            'expiry_date' => '2022-05-12',
            'event_id' => 10
        ]);

        $response->assertStatus(201);
    }

    public function test_configure_promo_code_radius()
    {
        $response = $this->putJson('api/radius/promo-code/2', [
            'radius' => 600
        ]);

        $response->assertStatus(200);
    }

    //Please note : the DB refreshes for this test there for promo_code wont exist for 
    //the test here, kindly refer to the Collections that is shipped with this code.
    public function test_use_promo_code()
    {
        $response = $this->postJson('api/promo', [
            'promo_code' => '8455',
            'pick_up' => 'mende, maryland',
            'pick_up_latitude' => '6.5764',
            'pick_up_longitude' => '3.3653',
            'destination' => 'stanbic bank maryland',
            'destination_latitude' => '6.5726391',
            'destination_longitude' => '3.3640866',
        ]);

        $response->assertStatus(200);
    }
    
}
