<?php

use App\Enums\AnnouncementStatus;

it('has all expected cases', function () {
    $values = array_column(AnnouncementStatus::cases(), 'value');
    $expectedValues = ['open', 'closed', 'expired'];

    sort($values);
    sort($expectedValues);
    expect($values)->toBe($expectedValues);
});

it('provides a label method returning a translation key', function () {
    expect(AnnouncementStatus::Open->label())->toBe('enums.announcement_status.open')
        ->and(AnnouncementStatus::Closed->label())->toBe('enums.announcement_status.closed');
});
