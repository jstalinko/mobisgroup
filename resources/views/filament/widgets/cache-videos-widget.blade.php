<x-filament::widget>
    <x-filament::card>
        
        <div class="flex justify-between items-center mb-4">
            <div>
                <h2 class="text-xl font-semibold">Cache Video Files</h2>
                <p class="text-sm text-gray-500">storage/app/videos/**/*.mp4</p>
            </div>

            <x-filament::button wire:click="scanFiles" color="primary" size="sm">
                Rescan
            </x-filament::button>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 py-2 text-left font-medium">File Path</th>
                        <th class="px-3 py-2 text-left font-medium">Size</th>
                        <th class="px-3 py-2 text-left font-medium">Modified</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-200">
                    @foreach ($this->files as $file)
                        <tr>
                            <td class="px-3 py-2">
                                <span class="font-medium">{{ $file['path'] }}</span>
                            </td>
                            <td class="px-3 py-2">
                                {{ $file['size_human'] }}
                            </td>
                            <td class="px-3 py-2">
                                {{ $file['modified']->diffForHumans() }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $this->files->links() }}
        </div>

    </x-filament::card>
</x-filament::widget>
