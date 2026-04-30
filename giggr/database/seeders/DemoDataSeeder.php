<?php

namespace Database\Seeders;

use App\Models\Announcement;
use App\Models\Favorite;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\App;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        if (! App::environment(['local', 'staging'])) {
            return;
        }

        $this->call([CitySeeder::class, InstrumentSeeder::class, GenreSeeder::class]);

        $users = User::factory()->count(30)->withProfile()->create();

        Announcement::factory()->count(50)->recycle($users)->create();

        $profiles = Profile::whereIn('user_id', $users->pluck('id'))->get();
        $announcements = Announcement::all();
        $favoritables = $profiles->map(fn ($p) => ['type' => $p->getMorphClass(), 'id' => $p->id])
            ->concat($announcements->map(fn ($a) => ['type' => $a->getMorphClass(), 'id' => $a->id])->all());

        $created = 0;
        $attempts = 0;

        while ($created < 100 && $attempts < 500) {
            $attempts++;
            $user = $users->random();
            $favoritable = $favoritables->random();

            $favorite = Favorite::firstOrCreate([
                'user_id' => $user->id,
                'favoritable_type' => $favoritable['type'],
                'favoritable_id' => $favoritable['id'],
            ]);

            if ($favorite->wasRecentlyCreated) {
                $created++;
            }
        }
    }
}
