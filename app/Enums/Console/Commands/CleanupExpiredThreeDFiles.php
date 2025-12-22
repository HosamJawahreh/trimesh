<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ThreeDFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class CleanupExpiredThreeDFiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'threed:cleanup-expired
                            {--dry-run : Show what would be deleted without actually deleting}
                            {--all : Delete all expired files regardless of age}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up expired 3D files that are older than 72 hours';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $isDryRun = $this->option('dry-run');

        $this->info('Starting cleanup of expired 3D files...');

        // Get all expired files from database
        $expiredFiles = ThreeDFile::expired()->get();

        if ($expiredFiles->isEmpty()) {
            $this->info('No expired files found.');
            return 0;
        }

        $this->info("Found {$expiredFiles->count()} expired file(s)");

        $deletedCount = 0;
        $failedCount = 0;

        foreach ($expiredFiles as $fileRecord) {
            $hoursExpired = now()->diffInHours($fileRecord->expiry_time, false);

            if ($isDryRun) {
                $this->line("Would delete: {$fileRecord->file_name} (ID: {$fileRecord->file_id}, expired {$hoursExpired} hours ago)");
                $deletedCount++;
                continue;
            }

            try {
                // Delete physical files
                $filesDeleted = [];

                if (Storage::disk('public')->exists($fileRecord->file_path)) {
                    Storage::disk('public')->delete($fileRecord->file_path);
                    $filesDeleted[] = 'data file';
                }

                if (Storage::disk('public')->exists($fileRecord->metadata_path)) {
                    Storage::disk('public')->delete($fileRecord->metadata_path);
                    $filesDeleted[] = 'metadata';
                }

                // Delete database record
                $fileRecord->delete();

                $filesDeletedStr = implode(', ', $filesDeleted);
                $this->info("✓ Deleted: {$fileRecord->file_name} (ID: {$fileRecord->file_id}, {$filesDeletedStr})");

                Log::info("Cleanup command deleted expired file: {$fileRecord->file_id}");
                $deletedCount++;

            } catch (\Exception $e) {
                $this->error("✗ Failed to delete: {$fileRecord->file_name} - {$e->getMessage()}");
                Log::error("Cleanup command failed to delete file {$fileRecord->file_id}: {$e->getMessage()}");
                $failedCount++;
            }
        }

        $this->newLine();

        if ($isDryRun) {
            $this->warn("DRY RUN: Would delete {$deletedCount} file(s)");
        } else {
            $this->info("Successfully deleted {$deletedCount} file(s)");

            if ($failedCount > 0) {
                $this->warn("Failed to delete {$failedCount} file(s)");
            }
        }

        return 0;
    }
}
