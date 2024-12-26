<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" rel="stylesheet">
    
    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/navtab.css') }}" rel="stylesheet">

    <!-- DataTables CSS -->
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/responsive/2.4.1/css/responsive.bootstrap5.min.css" rel="stylesheet">

    <!-- Select2 CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />

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

    <!-- DataTables Responsive Extension -->
    <script src="https://cdn.datatables.net/responsive/2.4.1/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.4.1/js/responsive.bootstrap5.min.js"></script>

    <!-- Select2 JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Aktifkan submenu
            document.querySelectorAll('.dropdown-submenu').forEach(function (element) {
                element.addEventListener('mouseenter', function () {
                    let submenu = this.querySelector('.dropdown-menu');
                    if (submenu) {
                        submenu.style.display = 'block';
                    }
                });

                element.addEventListener('mouseleave', function () {
                    let submenu = this.querySelector('.dropdown-menu');
                    if (submenu) {
                        submenu.style.display = 'none';
                    }
                });
            });
        });

    </script>
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-dark bg-dark shadow-sm">
            <div class="container">
                <!-- <a class="navbar-brand" href="{{ route('home') }}">
                    <img src="{{ asset('media/images/logo_ppi.png') }}" alt="MyApp Logo" width="50" height="50" class="d-inline-block align-top">
                </a> -->
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav me-auto">
                        @auth
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('home') }}">
                                    <i class="fa-solid fa-house"></i> Home
                                </a>
                            </li>

                            @if (auth()->user()->role == 'spg')
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('master.customer.index') }}">
                                    <i class="fa-solid fa-user" aria-hidden="true"></i> Customer
                                </a>
                            </li>
                            @endif

                            @if (auth()->user()->role == 'dev' OR auth()->user()->role == 'admin')
                            <!-- master -->
                            <li class="nav-item dropdown">
                                <a id="adminDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                    <i class="fa-solid fa-file"></i> Master
                                </a>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a class="dropdown-item" href="{{ route('master.customer.index') }}">
                                            <i class="fa-solid fa-user" aria-hidden="true"></i> Customer
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('master.vendor.index') }}">
                                            <i class="fa-solid fa-building"></i> Vendor
                                        </a>
                                    </li>
                                    
                                    <li class="dropdown-submenu">
                                        <a class="dropdown-item dropdown-toggle" href="#">
                                            <i class="fa-solid fa-layer-group"></i> Product
                                        </a>
                                        <ul class="dropdown-menu">
                                            <li><a class="dropdown-item" href="{{ route('product') }}"><i class="fa fa-list" aria-hidden="true"></i> List</a></li>
                                            <li><a class="dropdown-item" href="{{ route('stock_ga.index') }}"> <i class="fa-solid fa-boxes-packing" aria-hidden="true"></i> Stock GA</a></li>
                                        </ul>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('admin.users') }}">
                                            <i class="fa fa-users" aria-hidden="true"></i> Users
                                        </a>
                                    </li>
                                </ul>
                            </li>
                            @endif

                            <!-- Jurnal -->
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fa-solid fa-basket-shopping" aria-hidden="true"></i> Jurnal
                                </a>
                                <ul class="dropdown-menu" aria-labelledby="adminDropdown">
                                    <li>
                                        <a class="dropdown-item" href="{{ route('penjualan.create_senses') }}">
                                            <i class="fa-solid fa-shop" aria-hidden="true"></i> Senses
                                        </a>
                                        <a class="dropdown-item" href="{{ route('penjualan.create_gcf') }}">
                                            <i class="fa-solid fa-shop" aria-hidden="true"></i> GCF
                                        </a>
                                    </li>
                                </ul>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('penjualan.review') }}">
                                    <i class="fa-solid fa-eye"></i> Review
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('penjualan.settle') }}">
                                    <i class="fa-solid fa-check"></i> Settel
                                </a>
                            </li>
                        @endauth
                    </ul>

                    <ul class="navbar-nav ms-auto">
                        @guest
                            <!-- Guest links if needed (e.g., Login, Register) -->
                        @else
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                    <i class="fa-solid fa-user-tie"></i> {{ Auth::user()->name }}
                                </a>

                                <div class="dropdown-menu dropdown-menu-end">
                                    <a class="dropdown-item" href="{{ route('logout') }} "
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
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        @yield('content')
                    </div>
                </div>
            </div>
        </main>
    </div>

    @include('layouts.footer')
    @yield('scripts')
</body>
</html>