<?php

namespace App\Providers;

use App\Models\Announcement;
use App\Models\Profile;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        Relation::enforceMorphMap([
            'profile' => Profile::class,
            'announcement' => Announcement::class,
        ]);
    }
}
