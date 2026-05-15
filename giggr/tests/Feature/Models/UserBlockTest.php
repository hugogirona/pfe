<?php

use App\Models\Conversation;
use App\Models\User;

it('block creates a row in user_blocks', function () {
    $alice = User::factory()->withProfile()->create();
    $bob = User::factory()->withProfile()->create();

    $alice->block($bob);

    expect($alice->blockedUsers()->pluck('users.id')->all())->toContain($bob->id);
});

it('block is idempotent', function () {
    $alice = User::factory()->withProfile()->create();
    $bob = User::factory()->withProfile()->create();

    $alice->block($bob);
    $alice->block($bob);

    expect($alice->blockedUsers()->count())->toBe(1);
});

it('unblock removes the row', function () {
    $alice = User::factory()->withProfile()->create();
    $bob = User::factory()->withProfile()->create();
    $alice->block($bob);

    $alice->unblock($bob);

    expect($alice->blockedUsers()->count())->toBe(0);
});

it('hasBlocked returns true when blocked, false otherwise', function () {
    $alice = User::factory()->withProfile()->create();
    $bob = User::factory()->withProfile()->create();

    expect($alice->hasBlocked($bob))->toBeFalse();

    $alice->block($bob);

    expect($alice->hasBlocked($bob))->toBeTrue();
});

it('isBlockedBy is the reverse perspective', function () {
    $alice = User::factory()->withProfile()->create();
    $bob = User::factory()->withProfile()->create();

    expect($alice->isBlockedBy($bob))->toBeFalse();

    $bob->block($alice);

    expect($alice->isBlockedBy($bob))->toBeTrue()
        ->and($alice->hasBlocked($bob))->toBeFalse();
});

it('blocking does not silently block from your side', function () {
    $alice = User::factory()->withProfile()->create();
    $bob = User::factory()->withProfile()->create();

    $alice->block($bob);

    expect($alice->hasBlocked($bob))->toBeTrue()
        ->and($bob->hasBlocked($alice))->toBeFalse();
});

it('blocking refuses self-block', function () {
    $alice = User::factory()->withProfile()->create();

    expect(fn () => $alice->block($alice))->toThrow(InvalidArgumentException::class);
});

it('block hides any existing conversation in the blocker inbox', function () {
    $alice = User::factory()->withProfile()->create();
    $bob = User::factory()->withProfile()->create();
    $convo = Conversation::between($alice, $bob);

    $alice->block($bob);

    $pivot = $alice->fresh()->conversations()->find($convo->id)->pivot;
    expect($pivot->hidden_at)->not->toBeNull();
});

it('cascades on user delete', function () {
    $alice = User::factory()->withProfile()->create();
    $bob = User::factory()->withProfile()->create();
    $alice->block($bob);

    $alice->delete();

    expect(DB::table('user_blocks')->count())->toBe(0);
});
