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
    
    <!-- Range Date CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />

    <!-- datatable button CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.3.6/css/buttons.dataTables.min.css">

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
    
    <!-- Range Date JS -->
     <script src="https://cdn.jsdelivr.net/npm/moment/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

    <!-- datatable button JS -->
    <script src="https://cdn.datatables.net/buttons/2.3.6/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.html5.min.js"></script>

    <script>
        // Set notif auto close
        setTimeout(function() {
            $(".alert").fadeOut('slow');
        }, 4000);
    
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.dropdown > a').forEach(function (element) {
                element.addEventListener('click', function (e) {
                    if (window.innerWidth <= 768) {
                        e.preventDefault(); // Mencegah navigasi langsung
                        let submenu = this.nextElementSibling;
                        if (submenu) {
                            submenu.classList.toggle('show'); // Toggle tampilan submenu
                        }
                    }
                });
            });
        });
    
        document.addEventListener('DOMContentLoaded', function () {
            $('.dropdown-submenu > a').on("click", function(e) {
                var submenu = $(this).next('.dropdown-menu');
                if (submenu.is(':visible')) {
                    submenu.hide();
                } else {
                    $('.dropdown-submenu .dropdown-menu').hide(); // Tutup semua submenu lain
                    submenu.show();
                }
                e.stopPropagation(); // Mencegah event bubbling
                e.preventDefault(); // Mencegah navigasi default
            });
        
            // Menutup dropdown jika di-klik di luar
            $(document).on("click", function(e) {
                if (!$(e.target).closest('.dropdown-submenu').length) {
                    $('.dropdown-submenu .dropdown-menu').hide();
                }
            });
        });

    
        document.addEventListener('DOMContentLoaded', function () {
            // Fungsi untuk memuat notifikasi
            function loadNotifications() {
                $.ajax({
                    url: '{{ route("getNotifData") }}',
                    method: 'GET',
                    success: function (response) {
                        const notifList = $('#notifList');
                        const notifCount = $('#notifCount');
            
                        notifList.empty(); // Kosongkan elemen sebelum menambahkan data baru
                        let unreadCount = 0; // Menyimpan jumlah notifikasi belum dibaca
            
                        if (response.notifications && response.notifications.length > 0) {
                            response.notifications.forEach(function (notif) {
                                const message = notif.data?.message || 'No message';
                                const createdAt = notif.created_at 
                                    ? new Date(notif.created_at).toLocaleDateString('id-ID', { day: '2-digit', month: '2-digit', year: 'numeric' }) 
                                    : 'Unknown date';
                                
                                const isRead = notif.read_at !== null;
                                if (!isRead) unreadCount++; // Hitung notifikasi yang belum dibaca
                                
                                const notifClass = isRead ? 'text-muted' : 'fw-bold';
                                const notifItem = `
                                    <a href="#" class="list-group-item list-group-item-action ${notifClass}">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>${message}</div>
                                            <small>${createdAt}</small>
                                        </div>
                                    </a>
                                `;
                                notifList.append(notifItem);
                            });
            
                            // Perbarui jumlah badge berdasarkan jumlah notifikasi belum dibaca
                            if (unreadCount > 0) {
                                notifCount.text(unreadCount).show();
                            } else {
                                notifCount.hide();
                            }
                        } else {
                            notifList.append('<div class="text-center p-3">No notifications available.</div>');
                            notifCount.hide(); // Sembunyikan badge jika tidak ada notifikasi
                        }
                    },
                    error: function (xhr) {
                        if (xhr.status !== 401) {
                            console.error('Error loading notifications:', xhr.statusText);
                        }
                    },
                });
            }

            // Fungsi untuk menandai semua notifikasi sebagai telah dibaca
            function markAllAsRead() {
                $.ajax({
                    url: '{{ route("unread_all_notif") }}', // Pastikan route ini benar
                    method: 'POST',
                    data: { _token: '{{ csrf_token() }}' }, // Kirim CSRF token untuk keamanan
                    success: function (response) {
                        if (response.success) {
                            loadNotifications(); // Refresh notifikasi setelah berhasil
                        } else {
                            alert(response.message || 'Failed to mark notifications as read.');
                        }
                    },
                    error: function (xhr) {
                        // Tangani error 401 secara diam-diam tanpa pesan atau redirect
                        if (xhr.status !== 401) {
                            console.error('Error marking notifications as read:', xhr.statusText);
                        }
                    },
                });
            }

            // Panggil fungsi untuk memuat notifikasi secara berkala
            setInterval(loadNotifications, 5000); // Setiap 5 detik

            // Panggil fungsi untuk memuat notifikasi saat halaman dimuat
            loadNotifications();

            // Event listener untuk tombol "Mark All as Read"
            $('#markAllAsReadBtn').on('click', markAllAsRead);
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
                                    <li>
                                        <a class="dropdown-item" href="{{ route('master.wilayah.index') }}">
                                            <i class="fa fa-globe" aria-hidden="true"></i> Wilayah
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
                                <a class="nav-link" href="{{ route('penjualan.review_page') }}">
                                    <i class="fa-solid fa-eye"></i> Review
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('penjualan.settle_page') }}">
                                    <i class="fa-solid fa-check"></i> Settel
                                </a>
                            </li>

                            @if (auth()->user()->role == 'dev' OR auth()->user()->role == 'admin')
                            <li class="nav-item dropdown">
                                <a id="adminDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                    <i class="fa-solid fa-file"></i> Report
                                </a>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a class="dropdown-item" href="{{ route('report.jurnal_daily.index') }}">
                                            <i class="fa-solid fa-file" aria-hidden="true"></i> Jurnal Daily
                                        </a>
                                    </li>
                                </ul>
                            </li>
                            @endif
                        @endauth
                    </ul>

                    <ul class="navbar-nav ms-auto">
                        @guest
                            <!-- Guest links if needed (e.g., Login, Register) -->
                        @else
                            <!-- Notifikasi -->
                            <li class="nav-item dropdown">
                                <a
                                    class="nav-link dropdown-toggle"
                                    href="#"
                                    id="notificationDropdown"
                                    role="button"
                                    data-bs-toggle="dropdown"
                                    aria-expanded="false"
                                >
                                    <i class="fa-solid fa-bell"></i>
                                    <span id="notifCount" class="badge bg-danger" style="display: none;"></span>
                                </a>
                                <div class="dropdown-menu dropdown-menu-end p-0" aria-labelledby="notificationDropdown">
                                    <div class="card" style="width: 24rem; max-height: 400px; overflow-y: auto;">
                                        <div class="card-header d-flex justify-content-between align-items-center">
                                            <h5 class="card-title mb-0">Notifications</h5>
                                            <button type="button" class="btn btn-link btn-sm" id="markAllAsReadBtn">
                                                Mark All as Read
                                            </button>
                                        </div>
                                        <div class="list-group list-group-flush" id="notifList">
                                            <div class="text-center p-3">Loading...</div>
                                        </div>
                                    </div>
                                </div>
                            </li>

                            <!-- User name -->
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