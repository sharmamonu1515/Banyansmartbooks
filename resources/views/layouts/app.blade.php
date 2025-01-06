<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <link rel="stylesheet" href="//cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css">
    @yield('head')
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/magnific-popup.js/1.1.0/magnific-popup.min.css" />
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css"/>
    <link rel="stylesheet" href="{{ url('css/styles.css') }}">
    <script src="//code.jquery.com/jquery-3.6.1.min.js"></script>
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light bg-white @auth shadow-sm @endauth">
            <div class="container navbar-menu-container @guest logged-out @endguest">
                @guest
                    <div class="w-100 text-center">
                        <a class="navbar-brand" href="{{ url('/') }}">
                            <img src="{{ url('images/logo.png') }}" alt="{{ config('app.name', 'Laravel') }}" class="w-20 img-fluid">
                        </a>
                        <h3 class="pt-3">Welcome to Banyan Smart Books</h3>
                    </div>
                @else
                    <a class="navbar-brand" href="{{ url('/') }}">
                        <img src="{{ url('images/logo.png') }}" alt="{{ config('app.name', 'Laravel') }}" class="w-75 img-fluid">
                    </a>
                @endif
                {{-- <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button> --}}

                @auth
                    <div class="school-logo">
                        <a href="{{ auth()->user()->host()->website }}" class="">
                            <img src="{{ auth()->user()->host()->logo_image_url() }}" onerror="this.onerror=null;this.src='';" class="img-fluid">
                        </a>
                    </div>
                    <div class="logout-link">
                        @if (session('first_page', false) && session('first_page') != request()->route()->getName())
                            <button class="btn btn-secondary back-btn" onclick="window.history.back()"><i class="fa fa-angle-left"></i> Back</button>
                        @endif

                        @php
                            if (!session('first_page', false)) {
                                session(['first_page' => request()->route()->getName()]);
                            }
                        @endphp

                        <a class="text-decoration-none btn btn-primary" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <i class="fa fa-right-from-bracket"></i>
                            {{ __('Logout') }}
                        </a>

                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>

                    </div>
                @endauth




            </div>
        </nav>

        <main class="py-4">
            @yield('content')
        </main>
    </div>

    @auth
        @if (session('standard') && auth()->user()->host()->pgfootertxt)
            <footer class="text-center text-lg-start bg-light text-muted">
                <div class="text-center p-4" style="background-color: rgba(0, 0, 0, 0.05);">
                    <a class="text-reset fw-bold" href="{{ auth()->user()->host()->pgfooterurl }}">{{ auth()->user()->host()->pgfootertxt }}</a>
                </div>
            </footer>
        @endif
    @endauth
</body>
<script src="//cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/magnific-popup.js/1.1.0/jquery.magnific-popup.min.js"></script>
    @yield('scripts')
</html>
