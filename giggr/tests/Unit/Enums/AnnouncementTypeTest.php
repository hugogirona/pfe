<?php

use App\Enums\AnnouncementType;

it('has all expected cases', function () {
    $values = array_column(AnnouncementType::cases(), 'value');

    expect($values)
        ->toContain('search')
        ->toContain('formation')
        ->toContain('session')
        ->toContain('course')
        ->toContain('event');
});

it('provides a label method returning a translation key', function () {
    expect(AnnouncementType::Search->label())->toBe('enums.announcement_type.search')
        ->and(AnnouncementType::Event->label())->toBe('enums.announcement_type.event');
});
