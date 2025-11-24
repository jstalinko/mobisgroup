{{-- resources/views/filament/reusable-flash-header.blade.php --}}

@php
    $sessionKey = 'filament_action_message';
    $message = session($sessionKey);
@endphp
<div>
@if ($message)
    <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 p-6 mb-4">
        <h3 class="text-lg font-semibold tracking-tight text-gray-950 dark:text-white">
            SUCCESS ✅
        </h3>

        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
            data :
        </p>

        <div class="mt-2 p-3 bg-gray-100 dark:bg-gray-800 rounded-lg border border-gray-300 dark:border-gray-700">
            
                {!! $message !!}
            
        </div>
    </div>

    @php
        session()->forget($sessionKey);
    @endphp
@endif
</div>