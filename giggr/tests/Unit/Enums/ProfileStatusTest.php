<?php

use App\Enums\ProfileStatus;

it('has all expected cases', function () {
    $values = array_column(ProfileStatus::cases(), 'value');

    expect($values)
        ->toContain('looking_for_band')
        ->toContain('available_for_sessions')
        ->toContain('teaching')
        ->toContain('open_to_collab')
        ->toContain('not_available');
});

it('provides a label method returning a translation key', function () {
    expect(ProfileStatus::LookingForBand->label())->toBe('enums.profile_status.looking_for_band');
    expect(ProfileStatus::NotAvailable->label())->toBe('enums.profile_status.not_available');
});
