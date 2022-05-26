<?php

namespace Tests\Feature\Auth;

use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class Authenticate extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testLogin()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }
    public function testDataFail(){
        $response = $this->post('/login', [
            'email' => 'chuong@gmail.com',
            'password' => '12345678',
        ]);

        $response->assertStatus(302);

        $response->assertSessionHasErrors([
            'email' => __('auth.failed')
        ]);

        $this->assertGuest();
    }
    public function testDataSuccess(){
        $response = $this->post('/login', [
            'email' => 'chuongvu2806@gmail.com',
            'password' => '12345678',
        ]);

        $this->assertAuthenticated();
        $response->assertStatus(302);
        $response->assertRedirect(RouteServiceProvider::HOME);
    }
}
