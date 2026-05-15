<?php

use App\Enums\AnnouncementType;

it('has all expected cases', function () {
    $values = array_column(AnnouncementType::cases(), 'value');
    $expectedValues = ['musician_wanted', 'band_wanted', 'gig', 'lessons'];

    sort($values);
    sort($expectedValues);
    expect($values)->toBe($expectedValues);
});

it('provides a label method returning a translation key', function () {
    expect(AnnouncementType::MusicianWanted->label())->toBe('enums.announcement_type.musician_wanted')
        ->and(AnnouncementType::Lessons->label())->toBe('enums.announcement_type.lessons');
});
