<?php

use App\Enums\AnnouncementStatus;

it('has all expected cases', function () {
    $values = array_column(AnnouncementStatus::cases(), 'value');

    expect($values)
        ->toContain('open')
        ->toContain('closed')
        ->toContain('expired');
});

it('provides a label method returning a translation key', function () {
    expect(AnnouncementStatus::Open->label())->toBe('enums.announcement_status.open')
        ->and(AnnouncementStatus::Closed->label())->toBe('enums.announcement_status.closed');
});
