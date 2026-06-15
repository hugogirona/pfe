<?php

use App\Enums\ProfileStatus;

it('has all expected cases', function () {
    $values = array_column(ProfileStatus::cases(), 'value');

    expect($values)
        ->toContain('newcomer')
        ->toContain('looking_for_band')
        ->toContain('available_for_sessions')
        ->toContain('teaching')
        ->toContain('open_to_collab')
        ->toContain('not_available');
});

it('provides a label method returning a translation key', function () {
    expect(ProfileStatus::Newcomer->label())->toBe('enums.profile_status.newcomer')
        ->and(ProfileStatus::LookingForBand->label())->toBe('enums.profile_status.looking_for_band')
        ->and(ProfileStatus::NotAvailable->label())->toBe('enums.profile_status.not_available');
});

it('excludes the automatic newcomer state from the selectable statuses', function () {
    $selectable = ProfileStatus::selectable();

    expect($selectable)
        ->not->toContain(ProfileStatus::Newcomer)
        ->toContain(ProfileStatus::LookingForBand)
        ->toContain(ProfileStatus::NotAvailable)
        ->and(array_column($selectable, 'value'))->not->toContain('newcomer');
});
