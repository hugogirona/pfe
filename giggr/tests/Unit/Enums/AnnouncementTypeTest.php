<?php

use App\Enums\AnnouncementType;

it('has all expected cases', function () {
    $values = array_column(AnnouncementType::cases(), 'value');
    $expectedValues = ['search', 'formation', 'session', 'course', 'event'];

    sort($values);
    sort($expectedValues);
    expect($values)->toBe($expectedValues);
});

it('provides a label method returning a translation key', function () {
    expect(AnnouncementType::Search->label())->toBe('enums.announcement_type.search')
        ->and(AnnouncementType::Event->label())->toBe('enums.announcement_type.event');
});
