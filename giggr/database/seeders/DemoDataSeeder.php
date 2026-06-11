<?php

namespace Database\Seeders;

use App\Actions\UploadMediaImage;
use App\Enums\MediaType;
use App\Models\Announcement;
use App\Models\City;
use App\Models\Follow;
use App\Models\Media;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\App;
use Throwable;

class DemoDataSeeder extends Seeder
{
    private const string DEMO_YOUTUBE_ID = 'y6120QOlsfU';

    private const array DEMO_IMAGE_RGB = [102, 51, 153];

    private const int DEMO_IMAGE_WIDTH = 1200;

    private const int DEMO_IMAGE_HEIGHT = 800;

    /**
     * @throws Throwable
     */
    public function run(): void
    {
        if (! App::environment(['local', 'staging'])) {
            return;
        }

        $liege = City::where('name', 'Liège')->where('postal_code', '4020')->first();

        $developer = User::factory()->withProfile([
            'city_id' => $liege?->id,
            'birth_date' => '2000-02-14',
            'experience_years' => 12,
        ])->create([
            'first_name' => 'Hugo',
            'last_name' => 'Girona',
            'email' => 'hugo@giggr.be',
            'password' => 'change_this',
        ]);

        $jury = collect([
            ['first_name' => 'Dominique', 'last_name' => 'Vilain', 'email' => 'dvilain@giggr.be', 'password' => 'dvilain0626'],
            ['first_name' => 'François', 'last_name' => 'Parmentier', 'email' => 'fparmentier@giggr.be', 'password' => 'fparmentier0626'],
        ])->map(fn (array $attributes) => User::factory()->withProfile([
            'city_id' => $liege?->id,
            'birth_date' => null,
            'experience_years' => 8,
        ])->create($attributes));

        $featured = $jury->prepend($developer);

        $users = User::factory()->count(30)->withProfile()->create();

        foreach ($users->random(5) as $followed) {
            $featured->each(fn (User $user) => $user->follow($followed->profile));
        }
        foreach ($users->random(5) as $follower) {
            $featured->each(fn (User $user) => $follower->follow($user->profile));
        }

        $announcements = Announcement::factory()->count(50)->recycle($users)->create();
        $profiles = Profile::whereIn('user_id', $users->pluck('id'))->get();
        $followables = $profiles->map(fn ($p) => ['type' => $p->getMorphClass(), 'id' => $p->id])
            ->concat($announcements->map(fn ($a) => ['type' => $a->getMorphClass(), 'id' => $a->id])->all());

        $created = 0;
        $attempts = 0;

        while ($created < 100 && $attempts < 500) {
            $attempts++;
            $user = $users->random();
            $followable = $followables->random();

            $follow = Follow::firstOrCreate([
                'user_id' => $user->id,
                'followable_type' => $followable['type'],
                'followable_id' => $followable['id'],
            ]);

            if ($follow->wasRecentlyCreated) {
                $created++;
            }
        }

        $this->seedMedia($featured->map->profile, $profiles);
    }

    /**
     * @param  iterable<Profile>  $featuredProfiles
     * @param  iterable<Profile>  $otherProfiles
     *
     * @throws Throwable
     */
    private function seedMedia(iterable $featuredProfiles, iterable $otherProfiles): void
    {
        $tmpPath = tempnam(sys_get_temp_dir(), 'giggr-demo-media-').'.jpg';
        $img = imagecreatetruecolor(self::DEMO_IMAGE_WIDTH, self::DEMO_IMAGE_HEIGHT);
        imagefill($img, 0, 0, imagecolorallocate($img, ...self::DEMO_IMAGE_RGB));
        imagejpeg($img, $tmpPath, 80);

        $file = new UploadedFile($tmpPath, 'demo-photo.jpg', 'image/jpeg', null, true);

        $originalConnection = config('queue.default');
        config(['queue.default' => 'sync']);

        $featuredImage = null;

        try {
            foreach ($featuredProfiles as $profile) {
                $image = app(UploadMediaImage::class)->execute($profile, $file);
                $featuredImage ??= $image;
                $this->attachYoutube($profile);
            }
        } finally {
            config(['queue.default' => $originalConnection]);
        }

        foreach ($otherProfiles as $profile) {
            Media::create([
                'profile_id' => $profile->id,
                'type' => MediaType::Image,
                'source' => $featuredImage->source,
                'position' => 0,
                'width' => self::DEMO_IMAGE_WIDTH,
                'height' => self::DEMO_IMAGE_HEIGHT,
                'processed_at' => now(),
            ]);
            $this->attachYoutube($profile);
        }
    }

    private function attachYoutube(Profile $profile): void
    {
        Media::create([
            'profile_id' => $profile->id,
            'type' => MediaType::Youtube,
            'source' => self::DEMO_YOUTUBE_ID,
            'position' => 1,
            'processed_at' => now(),
        ]);
    }
}
