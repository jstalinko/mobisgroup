<!DOCTYPE html>
<html data-theme="synthwave">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />
    <meta name="csrf_token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@mdi/font@7.4.47/css/materialdesignicons.min.css">
    @vite('resources/css/app.css')
    @vite('resources/js/app.js')
    @include('seo')
    
    @inertiaHead
  </head>
  <body>
    
    @inertia
  </body>
</html>