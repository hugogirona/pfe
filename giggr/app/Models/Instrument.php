<?php

namespace App\Models;

use Database\Factories\InstrumentFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Instrument extends Model
{
    /** @use HasFactory<InstrumentFactory> */
    use HasFactory;

    protected $fillable = ['name', 'slug'];

    public function profiles(): BelongsToMany
    {
        return $this->belongsToMany(Profile::class);
    }
}
