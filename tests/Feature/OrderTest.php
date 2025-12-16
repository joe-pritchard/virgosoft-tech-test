<?php
declare(strict_types=1);

use App\Enums\AssetSymbol;
use App\Enums\OrderStatus;
use App\Models\Asset;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('can place a buy order and deducts the user balance', function () {
    $user = User::factory()->create(['balance' => 6000]);

    $this->actingAs($user)
        ->postJson(route('order.store'),
            ['symbol' => AssetSymbol::BTC, 'amount' => 0.1, 'side' => 'buy', 'price' => 50000])
        ->assertCreated()
        ->assertJsonFragment(['status' => OrderStatus::OPEN, 'symbol' => AssetSymbol::BTC]);

    $this->assertDatabaseHas('orders', [
        'user_id' => $user->id,
        'symbol' => AssetSymbol::BTC,
        'amount' => 0.1,
        'side' => 'buy',
        'price' => 50000,
        'status' => OrderStatus::OPEN,
    ]);

    expect($user->refresh()->balance)->toBe(1000.0);
});

it('refuses to place a buy order when the balance is too low', function () {
    $user = User::factory()->create(['balance' => 4000]);

    $this->actingAs($user)
        ->postJson(route('order.store'),
            ['symbol' => AssetSymbol::BTC, 'amount' => 0.1, 'side' => 'buy', 'price' => 50000])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['balance' => 'Insufficient balance']);

    expect($user->refresh()->balance)->toBe(4000.0);
    $this->assertDatabaseCount('orders', 0);
});

it('can place a sell order', function () {
    $user = User::factory()
        ->has(Asset::factory()->state([
            'symbol' => AssetSymbol::ETH,
            'amount' => 5,
            'locked_amount' => 0,
        ]))
        ->create();

    $this->actingAs($user)
        ->postJson(route('order.store'),
            ['symbol' => AssetSymbol::ETH, 'amount' => 1, 'side' => 'sell', 'price' => 3000])
        ->assertCreated()
        ->assertJsonFragment(['status' => OrderStatus::OPEN, 'symbol' => AssetSymbol::ETH]);

    $this->assertDatabaseHas('orders', [
        'user_id' => $user->id,
        'symbol' => AssetSymbol::ETH,
        'amount' => 1,
        'side' => 'sell',
        'price' => 3000,
        'status' => OrderStatus::OPEN,
    ]);

    $asset = $user->assets()->whereSymbol(AssetSymbol::ETH)->first();
    expect($asset->amount)
        ->toBe('4.00000000')
        ->and($asset->locked_amount)
        ->toBe('1.00000000');
});

it('refuses to place a sell order if the user does not hold the asset', function () {
    $user = User::factory()->create(['balance' => 1000]);

    $this->actingAs($user)
        ->postJson(route('order.store'),
            ['symbol' => AssetSymbol::ETH, 'amount' => 1, 'side' => 'sell', 'price' => 3000])
        ->assertNotFound();

    $this->assertDatabaseCount('orders', 0);
});

it('refuses to place a sell order if the user holds the asset but not enough', function () {
    $user = User::factory()
        ->has(Asset::factory()->state([
            'symbol' => AssetSymbol::ETH,
            'amount' => 5.0,
            'locked_amount' => 0.0,
        ]))
        ->create(['balance' => 1000.0]);

    $this->actingAs($user)
        ->postJson(route('order.store'),
            ['symbol' => AssetSymbol::ETH, 'amount' => 6, 'side' => 'sell', 'price' => 3000])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['amount' => 'Insufficient asset amount to place sell order']);

    $asset = $user->assets()->whereSymbol(AssetSymbol::ETH)->first();
    expect($asset->amount)->toBe('5.00000000')
        ->and($asset->locked_amount)->toBe('0.00000000');

    $this->assertDatabaseCount('orders', 0);
});

it('validates the order input', function () {
    $user = User::factory()->create(['balance' => 10000]);

    $this->actingAs($user)
        ->postJson(route('order.store'),
            ['symbol' => 'INVALID', 'amount' => 1, 'side' => 'buy', 'price' => 1])
        ->assertUnprocessable()
        ->assertJsonValidationErrors([
            'symbol' => 'The selected symbol is invalid.',
        ]);

    $this->actingAs($user)
        ->postJson(route('order.store'),
            ['symbol' => 'BTC', 'amount' => -1, 'side' => 'buy', 'price' => 1])
        ->assertUnprocessable()
        ->assertJsonValidationErrors([
            'amount' => 'The amount field must be at least 0.0001.',
        ]);

    $this->actingAs($user)
        ->postJson(route('order.store'),
            ['symbol' => 'BTC', 'amount' => 1, 'side' => 'INVALID', 'price' => 1])
        ->assertUnprocessable()
        ->assertJsonValidationErrors([
            'side' => 'The selected side is invalid.',
        ]);


    $this->actingAs($user)
        ->postJson(route('order.store'),
            ['symbol' => 'BTC', 'amount' => 1, 'side' => 'buy', 'price' => -1])
        ->assertUnprocessable()
        ->assertJsonValidationErrors([
            'price' => 'The price field must be at least 0.01.',
        ]);

    expect($user->refresh()->balance)->toBe(10000.0);
    $this->assertDatabaseCount('orders', 0);
});
