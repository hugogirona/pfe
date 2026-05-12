<?php

namespace App\Models;

use App\Enums\MediaType;
use Database\Factories\MediaFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Media extends Model
{
    /** @use HasFactory<MediaFactory> */
    use HasFactory;

    private const string YOUTUBE_EMBED_URL = 'https://www.youtube.com/embed/';

    private const string YOUTUBE_THUMBNAIL_URL = 'https://i.ytimg.com/vi/%s/hqdefault.jpg';

    private const string IMAGE_VARIANT_DIR = 'media';

    protected $table = 'media';

    protected $fillable = [
        'profile_id',
        'type',
        'source',
        'position',
        'caption',
        'width',
        'height',
    ];

    protected function casts(): array
    {
        return [
            'type' => MediaType::class,
            'position' => 'integer',
            'width' => 'integer',
            'height' => 'integer',
        ];
    }

    public function profile(): BelongsTo
    {
        return $this->belongsTo(Profile::class);
    }

    public function deleteVariants(): void
    {
        if ($this->type !== MediaType::Image) {
            return;
        }

        $disk = Storage::disk(config('media.disk', 'public'));
        foreach (array_keys(config('media.variants', [])) as $variant) {
            $disk->delete($this->variantPath($variant));
        }
    }

    protected function displayUrl(): Attribute
    {
        return Attribute::make(
            get: fn (): string => match ($this->type) {
                MediaType::Youtube => self::YOUTUBE_EMBED_URL.$this->source,
                MediaType::Image => $this->variantUrl('medium'),
            },
        );
    }

    protected function thumbnailUrl(): Attribute
    {
        return Attribute::make(
            get: fn (): ?string => $this->type === MediaType::Image
                ? $this->variantUrl('thumbnail')
                : null,
        );
    }

    protected function youtubeThumbnailUrl(): Attribute
    {
        return Attribute::make(
            get: fn (): ?string => $this->type === MediaType::Youtube
                ? sprintf(self::YOUTUBE_THUMBNAIL_URL, $this->source)
                : null,
        );
    }

    private function variantUrl(string $variant): string
    {
        return Storage::disk(config('media.disk', 'public'))->url($this->variantPath($variant));
    }

    private function variantPath(string $variant): string
    {
        return self::IMAGE_VARIANT_DIR."/{$variant}/{$this->source}.webp";
    }
}
