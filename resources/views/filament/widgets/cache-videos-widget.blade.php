<x-filament::widget>
    <x-filament::card>
        @php
            $data = $this->getData();
        @endphp

        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-lg font-medium">
                    Cache Videos
                </h3>
                <p class="text-sm text-gray-500">Scan: <code>storage/app/videos/**/*.mp4</code></p>
            </div>

            <div class="text-right">
                <div class="text-2xl font-bold">{{ number_format($data['count']) }}</div>
                <div class="text-sm text-gray-500">{{ $data['totalSizeHuman'] }}</div>
            </div>
        </div>

        <div class="mt-4">
            @if($data['error'])
                <div class="text-red-600 text-sm">Error: {{ $data['error'] }}</div>
            @elseif(count($data['latestFiles']) === 0)
                <div class="text-sm text-gray-500">Tidak ada file .mp4 ditemukan.</div>
            @else
                <div class="text-sm text-gray-600 mb-2">10 file terbaru</div>
                <ul class="space-y-2">
                    @foreach($data['latestFiles'] as $file)
                        <li class="flex items-center justify-between">
                            <div class="truncate pr-4">
                                <span class="text-sm font-medium">{{ $file['path'] }}</span>
                                <div class="text-xs text-gray-500">{{ $file['modified']->diffForHumans() }} • {{ $file['size_human'] }}</div>
                            </div>
                            <div class="text-xs text-gray-400">{{ $file['size_human'] }}</div>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </x-filament::card>
</x-filament::widget>
