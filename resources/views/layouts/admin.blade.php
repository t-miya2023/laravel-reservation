<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">
    <!--オリジナルCSS-->
    <link rel="stylesheet" href="{{ asset('/css/style.css') }}">
    <!--ファビコン-->
    <link rel="icon" href="{{ asset('/images/logo.jpg') }}">
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    <!--bootstrap icon-->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <!--マテリアルCSS-->
    <!--<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">-->
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light bg-warning shadow-sm">
            <div class="container">
                <div class="d-flex" style="align-items:center">
                    <a class="navbar-brand" style href="{{ url('/') }}">
                        {{ config('app.name', 'Laravel') }}
                        <img src="{{ asset('/images/logo.jpg') }}" style="width:30px" alt="">
                    </a>
                </div>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav me-auto">

                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ms-auto">
                        <!-- Authentication Links -->
                        @guest('admin')
                            @if (Route::has('admin.login'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ url('admin/login') }}">{{ __('Login') }}</a>
                                </li>
                            @endif

                            @if (Route::has('admin.register'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ url('admin/register') }}">{{ __('Register') }}</a>
                                </li>
                            @endif
                        @else
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                {{ Auth::guard('admin')->user()->name }} <!-- 管理者としてログインしている場合の表示 -->
                                </a>

                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{ route('admin.logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        {{ __('Logout') }}
                                    </a>

                                    <form id="logout-form" action="{{ route('admin.logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                    <a class="dropdown-item" href="{{ route('dashboard.index') }}">ダッシュボード</a>
                                </div>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        <main class="py-4">
            @yield('content')
        </main>
        <footer>
            <div class="d-flex justify-content-center align-items-center bg-light" style="height: 30px; z-index: auto;">
                <p class="small mb-0">&copy; MIYATA WORKS</p>
            </div>
        </footer>
    </div>
    @yield('script')
    <!--<script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>-->
</body>
</html>
