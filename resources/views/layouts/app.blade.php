<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" rel="stylesheet">
    
    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

    <!-- jQuery and Bootstrap (for DataTables) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- DataTables JS and Buttons Extension -->
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.print.min.js"></script>
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-dark bg-dark shadow-sm">
            <div class="container">
                <a class="navbar-brand" href="{{ route('home') }}">
                    <img src="{{ asset('media/images/logo_ppi.png') }}" alt="MyApp Logo" width="50" height="50" class="d-inline-block align-top">
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav me-auto">
                        @auth
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('home') }}">
                                    <i class="fa-solid fa-house"></i> Home
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('product') }}">
                                    <i class="fa-solid fa-list"></i> Product
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#">
                                    <i class="fa-solid fa-basket-shopping"></i> Sales
                                </a>
                            </li>
                            @if (auth()->user()->role == 'admin')
                            <li class="nav-item dropdown">
                                <a id="adminDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fa-solid fa-gears"></i> Setting
                                </a>
                                <ul class="dropdown-menu" aria-labelledby="adminDropdown">
                                    <li><a class="dropdown-item" href="{{ url('/admin/users') }}"></a></li>
                                    <li><a class="dropdown-item" href="{{ url('/admin/settings') }}"></a></li>
                                    <li><a class="dropdown-item" href="{{ url('/admin/reports') }}"></a></li>
                                </ul>
                            </li>
                            @endif
                        @endauth
                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ms-auto">
                        @guest
                            <!-- <li class="nav-item">
                                <a class="nav-link" href="{{ route('login') }}">Login</a>
                            </li> -->
                            <!-- <li class="nav-item">
                                <a class="nav-link" href="{{ route('register') }}">Register</a>
                            </li> -->
                        @else
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                    <i class="fa-solid fa-user-tie"></i> {{ Auth::user()->name }}
                                </a>

                                <div class="dropdown-menu dropdown-menu-end">
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                        <i class="fa-solid fa-right-from-bracket"></i> Logout
                                    </a>
                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
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
    </div>

    @include('layouts.footer')
    @yield('scripts')
</body>
</html>
