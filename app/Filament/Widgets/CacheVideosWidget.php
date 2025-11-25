<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use Illuminate\Support\Str;
use Carbon\Carbon;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;

class CacheVideosWidget extends Widget
{
    protected static string $view = 'filament.widgets.cache-videos-widget';

    // judul widget (opsional)
    protected static ?string $heading = 'Cache Videos';

    protected static int|null $sort = 2;

    protected  int|string|array $columnSpan = 'full';

    public function mount(): void
    {
        // nothing for now
    }

    public function getData(): array
    {
        $path = storage_path('app/videos');

        // Pastikan folder ada
        if (!is_dir($path)) {
            return [
                'count' => 0,
                'totalSize' => 0,
                'totalSizeHuman' => '0 B',
                'latestFiles' => [],
                'error' => null,
            ];
        }

        $count = 0;
        $totalBytes = 0;
        $files = [];

        try {
            $iterator = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($path, RecursiveDirectoryIterator::SKIP_DOTS)
            );

            foreach ($iterator as $file) {
                if ($file->isFile() && strtolower($file->getExtension()) === 'mp4') {
                    $count++;
                    $size = $file->getSize();
                    $totalBytes += $size;

                    $files[] = [
                        'path' => Str::after($file->getPathname(), $path . DIRECTORY_SEPARATOR),
                        'full_path' => $file->getPathname(),
                        'size' => $size,
                        'size_human' => $this->humanFilesize($size),
                        'modified' => Carbon::createFromTimestamp($file->getMTime()),
                    ];
                }
            }

            // urutkan terbaru dulu
            usort($files, function ($a, $b) {
                return $b['modified']->timestamp <=> $a['modified']->timestamp;
            });

            $latestFiles = array_slice($files, 0, 10);

            return [
                'count' => $count,
                'totalSize' => $totalBytes,
                'totalSizeHuman' => $this->humanFilesize($totalBytes),
                'latestFiles' => $latestFiles,
                'error' => null,
            ];
        } catch (\Throwable $e) {
            // kalau ada error permission / lain2
            return [
                'count' => 0,
                'totalSize' => 0,
                'totalSizeHuman' => '0 B',
                'latestFiles' => [],
                'error' => $e->getMessage(),
            ];
        }
    }

    protected function humanFilesize($bytes, $decimals = 2): string
    {
        if ($bytes === 0) {
            return '0 B';
        }

        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $i = floor(log($bytes, 1024));
        $human = $bytes / pow(1024, $i);

        return round($human, $decimals) . ' ' . $units[$i];
    }
}
