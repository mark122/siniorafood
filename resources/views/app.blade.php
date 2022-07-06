<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'QwikTest') }}</title>
        <meta name="description" content="{{ app(\App\Settings\SiteSettings::class)->seo_description }}">
        <link rel="icon" href="{{ url('storage/'.app(\App\Settings\SiteSettings::class)->favicon_path) }}">

        <!-- Fonts -->
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap">

        <!-- Styles -->
        <link rel="stylesheet" href="{{ asset('vendor/primeicons/primeicons.css') }}">
        <link rel="stylesheet" href="{{ asset('vendor/nprogress/nprogress.css') }}">
        <link rel="stylesheet" href="{{ asset('vendor/katex/katex.min.css') }}">
        <link rel="stylesheet" href="{{ asset('css/app.css') }}">

        <!-- Scripts -->
        @routes
        <script>
            window.CKEditorURL = "{{ asset('vendor/ckeditor/ckeditor.js') }}";
        </script>
        <script src="{{ asset('vendor/katex/katex.min.js') }}"></script>
        <script src="{{ asset('vendor/katex/contrib/auto-render.min.js') }}"></script>
        <script src="{{ asset('js/manifest.js') }}" defer></script>
        <script src="{{ asset('js/vendor.js') }}" defer></script>
        <script src="{{ asset('js/app.js') }}" defer></script>
        <style>
        .auth-background-overlay[data-v-6ccfbd76] {
            background-image: linear-gradient(159deg, rgb(239 84 77 / 95%), rgb(255 195 35 / 95%))!important;
            opacity: 0.92;
        }
        .text-green-300, .text-green-400, .text-green-500, .text-green-600, .text-green-700 {
            --tw-text-opacity: 1;
            color: #df6368!important;
        }
        .border-green-300, .border-green-400, .border-green-500, .border-green-600, .border-green-700 {
            --tw-text-opacity: 1;
            color: #df6368!important;
            border-color: #df6368!important;
        }
        .text-white:hover {
            --tw-text-opacity: 1;
            border-bottom-color: #df6368!important;
        }
        .bg-green-300,.bg-green-400,.bg-green-500,.bg-green-600,.bg-green-700 {
            --tw-bg-opacity: 1;
            background-color: #df6368!important;
        }
        .qt-link-success {
            cursor: pointer;
            --tw-text-opacity: 1;
            color: #df6268!important;
        }
        .qt-btn-success, .qt-btn {
                --tw-bg-opacity: 1;
            background-color: #df6368!important;
        }
        .p-tag {
            background: #df6268!important;
        }
        .w-6:hover {
            background: #df6268!important;
        }
        .pi {
            color: white!important;
        }
        .p-selectbutton .p-button.p-highlight, .p-button, .p-btn {
            background-color: #df6368!important;
            border-color: #df6368!important;
        }
        .p-selectbutton .p-button {
            background: #FFFFFF!important;
        }
          .bg-green-700  {
              background-image: linear-gradient(159deg, rgb(239 84 77 / 85%), rgb(255 195 35 / 75%)), url(https://hr.siniorafood.com/public/homefeed.jpg)!important;
              text-shadow: 1px 0.5px black;
        }
        .bg-gray-700 {
              background-color: #ffbb04!important;
        }
        .bg-green-100 {
            background-color: #ffbb04!important;
            color: #ec373b!important;
        }
        .welcomebox {
            margin-bottom:30px;
        }
        .text-xs {
        font-size: 0.95rem!important;
        }
        @media (min-width: 640px) {
        .sm\:h-12 {
        height: 4em!important;
        }
        }
        .bg-blue-50 {
    --tw-bg-opacity: 1;
    background-color: rgb(239, 246, 255, var(--tw-bg-opacity));
    background-image: linear-gradient(
159deg, rgb(239 246 225 / 11%), rgb(255 255 255 / 85%)), url(http://hr.siniorafood.com/public/bgfade.png)!important;
    background-size: cover;
}
        </style>
    </head>
    <body class="font-sans antialiased bg-blue-50">
        @inertia
    </body>
</html>
