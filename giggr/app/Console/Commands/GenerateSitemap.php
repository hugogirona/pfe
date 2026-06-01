<?php

namespace App\Console\Commands;

use App\Support\SitemapGenerator;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class GenerateSitemap extends Command
{
    protected $signature = 'sitemap:generate {path? : Output file path (defaults to public/sitemap.xml)}';

    protected $description = 'Generate the static XML sitemap';

    public function handle(SitemapGenerator $generator): int
    {
        $path = $this->argument('path') ?? public_path('sitemap.xml');

        File::put($path, $generator->toXml());

        $this->info("Sitemap written to {$path}");

        return self::SUCCESS;
    }
}
