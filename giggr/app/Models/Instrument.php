<?php

namespace App\Models;

use Database\Factories\InstrumentFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Instrument extends Model
{
    /** @use HasFactory<InstrumentFactory> */
    use HasFactory;

    protected $fillable = ['name', 'slug'];
}
