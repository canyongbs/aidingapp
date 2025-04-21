<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">

    <meta
        name="application-name"
        content="{{ config('app.name') }}"
    >
    <meta
        name="csrf-token"
        content="{{ csrf_token() }}"
    >
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1"
    >

    {{-- title kha se passs ho rha that i need to se --}}

    <title>{{ __('Help Center') }}</title>

    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>

    @filamentStyles

    @vite('resources/css/app.css')
</head>

<body class="antialiased">
    <div class="flex items-center justify-center">
        <div
            class="w-full max-w-7xl"
            id="app"
        >
        </div>
        @include('cookie-consent::index')
    </div>

    @filamentScripts
    @vite('resources/js/app.js')
</body>
<script>
    props = {
        accessUrl: "{{ route('portal.show') }}",
        userAuthenticationUrl: "{{ route('api.user.auth-check') }}",
        url: "{{ URL::to(URL::signedRoute('api.portal.define', [], false)) }}",
        searchUrl: "{{ URL::to(URL::signedRoute('api.portal.search', [], false)) }}",
        appUrl: "{{ config('app.url') }}",
        apiUrl: "{{ route('api.portal.define') }}",
        hostUrl: "{{ url('/') }}"
    };
</script>
<script
    src="{{ url('js/portals/knowledge-management/aiding-app-knowledge-management-portal.js?v=' . app('current-commit')) }}"
></script>

</html>
