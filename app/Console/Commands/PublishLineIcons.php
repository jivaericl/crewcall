<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class PublishLineIcons extends Command
{
    protected $signature = 'lineicons:publish';
    protected $description = 'Publish LineIcons SVG files to public directory';

    public function handle()
    {
        $source = base_path('node_modules/lineicons/assets/svgs/regular');
        $destination = public_path('vendor/lineicons');

        if (!File::exists($source)) {
            $this->error('LineIcons source directory not found. Please run: npm install lineicons');
            return 1;
        }

        // Create destination directory if it doesn't exist
        if (!File::exists($destination)) {
            File::makeDirectory($destination, 0755, true);
        }

        // Copy all SVG files
        File::copyDirectory($source, $destination);

        $count = count(File::files($destination));
        $this->info("Successfully published {$count} LineIcons to public/vendor/lineicons");

        return 0;
    }
}
