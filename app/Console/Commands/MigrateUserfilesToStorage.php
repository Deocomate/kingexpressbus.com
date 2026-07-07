<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class MigrateUserfilesToStorage extends Command
{
    protected $signature = 'media:migrate-userfiles
                            {--dry-run : Preview actions without copying files}
                            {--delete-source : Remove public/userfiles after successful copy}';

    protected $description = 'Migrate legacy public/userfiles uploads into storage/app/public/media';

    public function handle(): int
    {
        $sourceRoot = public_path('userfiles');
        $uploadsDirectory = config('assets.uploads_directory', 'media');
        $destinationRoot = storage_path('app/public/'.$uploadsDirectory);

        if (! File::isDirectory($sourceRoot)) {
            $this->info('public/userfiles does not exist — nothing to migrate.');

            return self::SUCCESS;
        }

        $copied = 0;
        $skipped = 0;
        $errors = 0;

        $files = File::allFiles($sourceRoot);

        foreach ($files as $file) {
            $relativePath = $file->getRelativePathname();
            $destination = $destinationRoot.DIRECTORY_SEPARATOR.$relativePath;

            if (File::exists($destination) && File::size($destination) === $file->getSize()) {
                $this->line("skip  {$relativePath}");
                $skipped++;

                continue;
            }

            if ($this->option('dry-run')) {
                $this->line("copy  {$relativePath} → {$uploadsDirectory}/{$relativePath}");
                $copied++;

                continue;
            }

            try {
                File::ensureDirectoryExists(dirname($destination));
                File::copy($file->getPathname(), $destination);
                $this->line("copy  {$relativePath}");
                $copied++;
            } catch (\Throwable $exception) {
                $this->error("error {$relativePath}: {$exception->getMessage()}");
                $errors++;
            }
        }

        $this->newLine();
        $this->info("Summary: copied={$copied}, skipped={$skipped}, errors={$errors}");

        if ($errors > 0) {
            return self::FAILURE;
        }

        if ($this->option('delete-source') && ! $this->option('dry-run')) {
            File::deleteDirectory($sourceRoot);
            $this->info('Deleted public/userfiles.');
        }

        return self::SUCCESS;
    }
}
