<?php

namespace Database\Seeders;

use App\Models\Announcement;
use App\Models\City;
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

        $liege = City::where('name', 'Liège')->where('postal_code', '4020')->first();

        User::factory()->withProfile([
            'city_id' => $liege?->id,
            'birth_date' => '2000-02-14',
            'experience_years' => 12,
        ])->create([
            'first_name' => 'Hugo',
            'last_name' => 'Girona',
            'email' => 'hello@giggr.com',
            'password' => 'change_this',
        ]);

        $users = User::factory()->count(30)->withProfile()->create();

        $announcements = Announcement::factory()->count(50)->recycle($users)->create();
        $profiles = Profile::whereIn('user_id', $users->pluck('id'))->get();
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
