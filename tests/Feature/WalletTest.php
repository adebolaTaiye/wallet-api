<?php

use App\Models\User;
use App\Models\Wallet;
use App\Models\Transaction;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('test that user can check balance', function () {

    $user = User::factory()->create();

    Sanctum::actingAs($user);

   $response = $this->getJson('/api/v1/wallet/balance');

    $response->assertStatus(200);
    $response->dump();


});

test('test that user can check transaction history', function () {

    $user = User::factory()->create();

    Sanctum::actingAs($user);

   $response = $this->getJson('/api/v1/wallet/transactions');

    $response->assertStatus(200);



});

test('test that user can purchase airtime', function () {

    $user = User::factory()->create();

    Sanctum::actingAs($user);

    $wallet = Wallet::where('user_id',$user->id);

    $wallet->update(['balance' => '7000']);

   $response = $this->postJson('api/v1/purchase/airtime', ['number' => '09094067590','amount' => '200']);

    $response->assertStatus(200);
    $response->dump();

});

test('test that user cannot purchase airtime because of insufficient funds', function () {

    $user = User::factory()->create();

    Sanctum::actingAs($user);

   $response = $this->postJson('api/v1/purchase/airtime', ['number' => '09094067590','amount' => '200']);

    $response->assertStatus(200);
    $response->dump();

});


