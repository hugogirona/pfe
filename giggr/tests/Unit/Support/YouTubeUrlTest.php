<?php

namespace Tests\Unit\Support;

use App\Support\YouTubeUrl;

it('extracts the id from a standard watch url', function () {
    expect(YouTubeUrl::extractId('https://www.youtube.com/watch?v=cj9kbTU9pKA'))
        ->toBe('cj9kbTU9pKA');
});

it('extracts the id from a short youtu.be url', function () {
    expect(YouTubeUrl::extractId('https://youtu.be/cj9kbTU9pKA'))
        ->toBe('cj9kbTU9pKA');
});

it('extracts the id from an embed url', function () {
    expect(YouTubeUrl::extractId('https://www.youtube.com/embed/cj9kbTU9pKA'))
        ->toBe('cj9kbTU9pKA');
});

it('rejects a raw 11-char id without url wrapping', function () {
    expect(YouTubeUrl::extractId('cj9kbTU9pKA'))->toBeNull();
});

it('extracts the id even when extra query params are present', function () {
    expect(YouTubeUrl::extractId('https://www.youtube.com/watch?v=cj9kbTU9pKA&t=42s'))
        ->toBe('cj9kbTU9pKA');
});

it('extracts the id even when v= is not the first query param', function () {
    expect(YouTubeUrl::extractId('https://www.youtube.com/watch?feature=share&v=cj9kbTU9pKA'))
        ->toBe('cj9kbTU9pKA');
});

it('handles ids with hyphens and underscores', function () {
    expect(YouTubeUrl::extractId('https://www.youtube.com/watch?v=AB_cd-EF1gh'))
        ->toBe('AB_cd-EF1gh');
});

it('trims surrounding whitespace', function () {
    expect(YouTubeUrl::extractId('  https://youtu.be/cj9kbTU9pKA  '))
        ->toBe('cj9kbTU9pKA');
});

it('accepts http (not just https)', function () {
    expect(YouTubeUrl::extractId('http://www.youtube.com/watch?v=cj9kbTU9pKA'))
        ->toBe('cj9kbTU9pKA');
});

it('accepts the m.youtube.com mobile subdomain', function () {
    expect(YouTubeUrl::extractId('https://m.youtube.com/watch?v=cj9kbTU9pKA'))
        ->toBe('cj9kbTU9pKA');
});

it('returns null for a non-youtube url', function () {
    expect(YouTubeUrl::extractId('https://vimeo.com/cj9kbTU9pKA'))->toBeNull();
});

it('returns null for an id shorter than 11 chars', function () {
    expect(YouTubeUrl::extractId('https://www.youtube.com/watch?v=tooshort'))->toBeNull();
});

it('returns null for a watch url without v= param', function () {
    expect(YouTubeUrl::extractId('https://www.youtube.com/watch'))->toBeNull();
});

it('returns null for empty input', function () {
    expect(YouTubeUrl::extractId(''))->toBeNull()
        ->and(YouTubeUrl::extractId('   '))->toBeNull();
});

it('returns null for any non-url string', function () {
    expect(YouTubeUrl::extractId('not-a-valid-id!'))->toBeNull()
        ->and(YouTubeUrl::extractId('shortid'))->toBeNull()
        ->and(YouTubeUrl::extractId('thisidistoolongtoyoutube'))->toBeNull();
});
