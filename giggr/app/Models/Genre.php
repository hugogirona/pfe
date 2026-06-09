<?php

namespace App\Models;

use App\Models\Concerns\HasTranslatedName;
use Database\Factories\GenreFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Genre extends Model
{
    /** @use HasFactory<GenreFactory> */
    use HasFactory, HasTranslatedName;

    protected $fillable = ['name', 'slug'];

    protected function translationNamespace(): string
    {
        return 'genres';
    }

    public function profiles(): BelongsToMany
    {
        return $this->belongsToMany(Profile::class);
    }

    public function announcements(): BelongsToMany
    {
        return $this->belongsToMany(Announcement::class);
    }
}
