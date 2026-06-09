<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Lang;

trait HasTranslatedName
{
    /**
     * Localised label for the catalogue entry, keyed by its stable slug.
     * Falls back to the stored name when no translation exists.
     */
    protected function translatedName(): Attribute
    {
        return Attribute::make(
            get: function (): string {
                $key = $this->translationNamespace().'.'.$this->slug;

                return Lang::has($key) ? __($key) : $this->name;
            },
        );
    }

    abstract protected function translationNamespace(): string;
}
