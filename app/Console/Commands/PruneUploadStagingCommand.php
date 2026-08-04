<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class PruneUploadStagingCommand extends Command
{
    protected $signature = 'admin:prune-upload-staging {--hours=24 : Delete directories older than this many hours}';
    protected $description = 'Remove stale admin-tmp upload directories older than the configured age.';

    public function handle(): int
    {
        $hours = (int) $this->option('hours');
        $cutoff = now()->subHours($hours)->timestamp;
        $disk = Storage::disk('local');
        $deleted = 0;
        $kept = 0;

        if (! $disk->exists('admin-tmp')) {
            $this->info('admin-tmp directory does not exist — nothing to prune.');
            return self::SUCCESS;
        }

        // admin-tmp/{session}/{uuid}/ — iterate session dirs
        foreach ($disk->directories('admin-tmp') as $sessionDir) {
            foreach ($disk->directories($sessionDir) as $uuidDir) {
                $mtime = $disk->lastModified($uuidDir);
                if ($mtime < $cutoff) {
                    $disk->deleteDirectory($uuidDir);
                    $deleted++;
                } else {
                    $kept++;
                }
            }

            // Remove empty session directories
            if (empty($disk->allFiles($sessionDir))) {
                $disk->deleteDirectory($sessionDir);
            }
        }

        $this->info("Pruned {$deleted} stale upload director" . ($deleted === 1 ? 'y' : 'ies') . "; kept {$kept}.");

        return self::SUCCESS;
    }
}
