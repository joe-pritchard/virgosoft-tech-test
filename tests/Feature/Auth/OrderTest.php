<?php
declare(strict_types=1);

use App\Enums\AssetSymbol;
use App\Models\Asset;
use App\Models\User;

it('can place a buy order', function () {
    $user = User::factory()->create(['balance' => 1000]);

    $this->actingAs($user)
        ->postJson(route('orders.store'),
            ['symbol' => AssetSymbol::BTC, 'amount' => 0.1, 'side' => 'buy', 'price' => 50000])
        ->assertCreated()
        ->assertJsonStructure(['id', 'status']);

    $this->assertDatabaseHas('orders', [
        'user_id' => $user->id,
        'symbol' => AssetSymbol::BTC,
        'amount' => 0.1,
        'side' => 'buy',
        'price' => 50000,
        'status' => 'open', // todo: use enum
    ]);

    expect($user->balance)->toBe(1000 - (0.1 * 50000));
});

it('can place a sell order', function () {
    $user = User::factory()
        ->has(Asset::factory()->make([
            'symbol' => AssetSymbol::ETH,
            'amount' => 5,
            'locked_amount' => 0,
        ]))
        ->create(['balance' => 1000]);

    $this->actingAs($user)
        ->postJson(route('orders.store'),
            ['symbol' => AssetSymbol::ETH, 'amount' => 1, 'side' => 'sell', 'price' => 3000])
        ->assertCreated()
        ->assertJsonStructure(['id', 'status']);

    $this->assertDatabaseHas('orders', [
        'user_id' => $user->id,
        'symbol' => AssetSymbol::ETH,
        'amount' => 1,
        'side' => 'sell',
        'price' => 3000,
        'status' => 'open', // todo: use enum
    ]);

    $asset = $user->assets()->whereSymbol(AssetSymbol::ETH)->first();
    expect($user->balance)->toBe(1000)
        ->and($asset->amount)
        ->toBe(4)
        ->and($asset->locked_amount)
        ->toBe(1);
});
