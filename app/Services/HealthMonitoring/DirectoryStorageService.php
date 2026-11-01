<?php

namespace App\Services\HealthMonitoring;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class DirectoryStorageService
{
    /**
     * Directories worth breaking down, relative to the app base path — the
     * usual suspects for "where did the disk space go" on a Laravel app.
     */
    private const DIRECTORIES = [
        'storage/logs' => 'Storage / Logs',
        'storage/app' => 'Storage / App Files',
        'storage/framework' => 'Storage / Framework Cache',
        'public/storage' => 'Public / Uploaded Files',
        'vendor' => 'Vendor (Composer)',
        'node_modules' => 'Node Modules',
        'database' => 'Database (SQLite files, if any)',
        'bootstrap/cache' => 'Bootstrap Cache',
    ];

    /**
     * Recursively sums each configured directory's size. This walks every file
     * under vendor/node_modules, so it's deliberately NOT called on every page
     * load — only from the scheduled collection command, same as every other
     * expensive check on this dashboard.
     */
    public function getDirectorySizes(): array
    {
        $sizes = [];

        foreach (self::DIRECTORIES as $relativePath => $label) {
            $path = base_path($relativePath);

            if (!is_dir($path)) {
                continue;
            }

            $bytes = $this->directorySize($path);

            $sizes[] = [
                'label' => $label,
                'path' => $relativePath,
                'size_mb' => round($bytes / 1024 / 1024, 2),
            ];
        }

        usort($sizes, fn ($a, $b) => $b['size_mb'] <=> $a['size_mb']);

        return $sizes;
    }

    private function directorySize(string $path): int
    {
        $total = 0;

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($path, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::LEAVES_ONLY,
            RecursiveIteratorIterator::CATCH_GET_CHILD // skip unreadable subdirectories instead of throwing
        );

        foreach ($iterator as $file) {
            if ($file->isFile() && !$file->isLink()) {
                $total += $file->getSize();
            }
        }

        return $total;
    }
}
