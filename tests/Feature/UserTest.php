<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;


uses(RefreshDatabase::class);

 test('test register route is successful', function() {

     $response = $this->postJson('/api/v1/register', ['name' =>'Dan Jones','email'=>'mazie2@gmail.com','password' =>'12345678','password_confirmation' =>'12345678']);

     $response->assertStatus(200);

     $this->assertNotNull($response->json('token'));

 });

 test('test login route is successful', function() {
    $user = User::factory()->create([
        'email' => 'test@example.com',
        'password' => bcrypt('password')
    ]);

    $data = [
        'email' => 'test@example.com',
        'password' => 'password'
    ];

     $response = $this->postJson('/api/v1/login',$data);
     $response->assertStatus(200);
     $this->assertNotNull($response->json('token'));
 });

test('test logout is successful', function() {

    $user = User::factory()->create();

          Sanctum::actingAs($user);

         $response = $this->postJson('/api/v1/logout');

    $response->assertStatus(200);
    $response->dump();
});

