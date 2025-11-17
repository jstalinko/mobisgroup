@php
    // Ambil file JSON dari storage
    $settings = [];

    try {
        $json = Storage::disk('local')->get('settings.json');
        $settings = json_decode($json, true);
    } catch (\Exception $e) {
        $settings = [];
    }

    $siteName = $settings['site_name'] ?? 'Website';
    $siteDescription = $settings['site_description'] ?? '';
    $siteUrl = $settings['site_url'] ?? url('/');
    $icon = $settings['icon'] ? asset('storage/' . $settings['icon']) : null;
    $logo = $settings['logo'] ? asset('storage/' . $settings['logo']) : null;

    // Meta custom
    $extraMeta = $settings['meta_setting'] ?? [];
@endphp

{{-- TITLE --}}
<title>{{ $siteName }}</title>

{{-- BASIC META --}}
<meta name="description" content="{{ $siteDescription }}">

{{-- FAVICON --}}
@if ($icon)
    <link rel="icon" href="{{ $icon }}">
@endif

{{-- OPEN GRAPH --}}
<meta property="og:title" content="{{ $siteName }}">
<meta property="og:description" content="{{ $siteDescription }}">
<meta property="og:url" content="{{ $siteUrl }}">
@if ($logo)
    <meta property="og:image" content="{{ $logo }}">
@endif
<meta property="og:type" content="website">

{{-- TWITTER --}}
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $siteName }}">
<meta name="twitter:description" content="{{ $siteDescription }}">
@if ($logo)
    <meta name="twitter:image" content="{{ $logo }}">
@endif

{{-- EXTRA META SETTINGS --}}
@if (!empty($extraMeta))
    @foreach ($extraMeta as $meta)
        @if (isset($meta['name']) && isset($meta['content']))
            <meta name="{{ $meta['name'] }}" content="{{ $meta['content'] }}">
        @endif
    @endforeach
@endif
<meta name="site_setting" content="{{ json_encode($settings) }}">
