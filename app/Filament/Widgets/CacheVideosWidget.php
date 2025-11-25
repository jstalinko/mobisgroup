<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use Livewire\WithPagination;
use Illuminate\Support\Str;
use Carbon\Carbon;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;
use Illuminate\Pagination\LengthAwarePaginator;

class CacheVideosWidget extends Widget
{
    use WithPagination;

    protected static string $view = 'filament.widgets.cache-videos-widget';
    protected ?string $heading = 'Cache Videos';
    protected int|string|array $columnSpan = 'full';
    public array $allFiles = [];
    public int $perPage = 10;

    public function mount()
    {
        $this->scanFiles();
    }

    public function scanFiles()
    {
        $path = storage_path('app/videos');

        if (!is_dir($path)) {
            $this->allFiles = [];
            return;
        }

        $files = [];

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($path, RecursiveDirectoryIterator::SKIP_DOTS)
        );

        foreach ($iterator as $file) {
            if ($file->isFile() && strtolower($file->getExtension()) === 'mp4') {
                $files[] = [
                    'path' => Str::after($file->getPathname(), $path . DIRECTORY_SEPARATOR),
                    'size' => $file->getSize(),
                    'size_human' => $this->humanFilesize($file->getSize()),
                    'modified' => Carbon::createFromTimestamp($file->getMTime()),
                ];
            }
        }

        // sort by modified desc
        usort($files, fn ($a, $b) => $b['modified']->timestamp <=> $a['modified']->timestamp);

        $this->allFiles = $files;
    }

    public function getFilesProperty()
    {
        $page = $this->page;
        $perPage = $this->perPage;
        $items = $this->allFiles;

        $offset = ($page - 1) * $perPage;

        return new LengthAwarePaginator(
            array_slice($items, $offset, $perPage),
            count($items),
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );
    }

    protected function humanFilesize($bytes)
    {
        if ($bytes <= 0) return '0 B';

        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $i = floor(log($bytes, 1024));

        return round($bytes / (1024 ** $i), 2) . ' ' . $units[$i];
    }
}
