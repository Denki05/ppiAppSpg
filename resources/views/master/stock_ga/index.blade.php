@extends('layouts.app')

@section('content')
<div class="container">
    <!-- Notifikasi -->
    @if(session()->has('collect_success') || session()->has('collect_error'))
        <div class="container">
            <div class="row">
                <div class="col pl-0">
                    <div class="alert alert-success alert-dismissable" role="alert" style="max-height: 300px; overflow-y: auto;">
                        <h3 class="alert-heading font-size-h4 font-w400">Successful Import</h3>
                        @if(session()->has('collect_success'))
                            @foreach (session()->get('collect_success', []) as $msg)
                                <p class="mb-0">{{ $msg }}</p>
                            @endforeach
                        @endif
                    </div>
                </div>
                <div class="col pr-0">
                    <div class="alert alert-danger alert-dismissable" role="alert" style="max-height: 300px; overflow-y: auto;">
                        <h3 class="alert-heading font-size-h4 font-w400">Failed Import</h3>
                        @if(session()->has('collect_error'))
                            @foreach (session()->get('collect_error', []) as $msg)
                                <p class="mb-0">{{ $msg }}</p>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif

    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}"><i class="fa-solid fa-house"></i> Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Product</li>
            <li class="breadcrumb-item active" aria-current="page">Stock GA</li>
        </ol>
    </nav>

    <div class="row justify-content-center">
        <div class="col-md-12">
            <button type="button" class="btn btn-success" id="openGAModal"><i class="fa fa-plus"></i> Tambah</button>
            
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#importExportModal">
                Manage
            </button>
            <br><br>
            <table id="variantStock" class="table table-striped">
                <thead>
                    <tr>
                        <th class="text-center">#</th>
                        <th class="text-center">SPG</th>
                        <th class="text-center">Brand</th>
                        <th class="text-center">Variant</th>
                        <th class="text-center">Botol</th>
                        <th class="text-center">Volume</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($stocks as $index => $stock)
                        @php
                            // Find the product details by matching product_id
                            $product = collect($products)->firstWhere('id', $stock->product_id);
                        @endphp
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td class="text-center">{{ $stock->user->name }}</td>
                            <td class="text-center">{{ $stock->brand_name }}</td>
                            <td class="text-center">
                                @if($product)
                                    {{ $product['code'] }} - {{ $product['name'] }}
                                @else
                                    <em>Unknown Product</em>
                                @endif
                            </td>
                            <td class="text-center">{{ $stock->pcs }}</td>
                            <td class="text-center">{{ $stock->qty }} (ML)</td>
                            <td class="text-center">
                                @if($stock->id)
                                    <button class="btn btn-success btn-sm" onclick="addStock({{ $stock->id }})">
                                        <i class="fa fa-plus"></i> Add Stock
                                    </button>
                                @else
                                    <em>No ID available</em>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- input stok awal -->
<div class="modal fade" id="gaModal" tabindex="-1" aria-labelledby="gaModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="gaModalLabel">Tambah Item Give Away</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="gaForm">
                    <div class="mb-3">
                        <label for="brandSelect" class="form-label">SPG</label>
                        <select class="form-control select2" id="spgSelect" name="spg">
                            <option value="">Pilih SPG</option>
                            @foreach($user as $row)
                            <option value="{{ $row->id }}">{{ $row->email }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="brandSelect" class="form-label">Variant</label>
                        <select class="form-control select2" id="brandSelect" name="brand">
                            <option value="">Pilih Brand</option>
                            <option value="Senses">Senses</option>
                            <option value="GCF">GCF</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="variantSelect" class="form-label">Variant</label>
                        <select class="form-control select2" id="variantSelect" name="variant">
                            <option value="">Pilih Variant</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="qtyInput" class="form-label">Botol</label>
                        <input type="number" class="form-control" id="qtyInput" name="botol" min="1" placeholder="Masukkan jumlah botol" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="saveGA">Simpan</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal for Adding Stock -->
<div class="modal fade" id="addStockModal" tabindex="-1" aria-labelledby="addStockModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addStockModalLabel">Tambah Stock</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addStockForm">
                    <div class="mb-3">
                        <label for="additionalQty" class="form-label">Jumlah Tambahan</label>
                        <input type="number" class="form-control" id="additionalQty" name="additional_stock" min="1" placeholder="Enter additional quantity" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" id="saveStock">Add Stock</button>
            </div>
        </div>
    </div>
</div>

<!-- Import Stock GA -->
<div class="modal fade" id="importExportModal" tabindex="-1" aria-labelledby="importExportModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="importExportModalLabel">Manage Import & Export Stock GA</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('stock_ga.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label for="file" class="form-label">Import Excel File</label>
                        <input type="file" name="file" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-success">Import</button>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                <a href="{{ route('stock_ga.export') }}" class="btn btn-info">Download Template</a>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    let stockIdToUpdate = null; // Store the ID of the stock to update

    function addStock(stockId) {
        console.log('Stock ID:', stockId); // Debug stock ID
        stockIdToUpdate = stockId;
        if (!stockId) {
            alert('Invalid stock ID.');
            return;
        }
        $('#addStockModal').modal('show'); // Show the modal
    }

    $(document).ready(function () {
        document.addEventListener("DOMContentLoaded", function() {
            setTimeout(function() {
                let alerts = document.querySelectorAll(".alert");
                alerts.forEach(alert => {
                    let bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                });
            }, 5000); // 5000ms = 5 detik
        });
        
        $('#spgSelect').select2({
            width: '100%',
            dropdownParent: $('#gaModalLabel'), // Ensures the dropdown stays within the modal
        });
        
        $('#brandSelect').select2({
            width: '100%',
            dropdownParent: $('#gaModalLabel'), // Ensures the dropdown stays within the modal
        });

        $('#variantSelect').select2({
            width: '100%',
            dropdownParent: $('#gaModalLabel'), // Ensures the dropdown stays within the modal
        });
        
        $('#variantStock').DataTable({
           paging: true,
           pageLength: 5,
           lengthMenu: [5, 25, 50, 100],
           order: [[1, 'asc']],
           responsive: true, // Enable responsiveness
           columnDefs: [
               { targets: [0, 5], orderable: false } // Disable sorting on # and Action columns
           ]
       });

        var product_data = []; // Declare product_data globally

        function preloadProducts(brand) {
            if (!brand) {
                return;
            }

            return $.ajax({
                url: `/product/brand/${brand}`,
                type: "GET",
                success: function (response) {
                    product_data = response;
                },
                error: function (error) {
                    console.error('Failed to preload products:', error);
                }
            });
        }

        $('#brandSelect').on('change', function () {
            let brand = $(this).val();
            preloadProducts(brand).then(() => {
                const variantSelect = $('#variantSelect');
                variantSelect.empty().append('<option value="">Pilih Variant</option>');

                if (product_data.length > 0) {
                    product_data.forEach(product => {
                        variantSelect.append(`<option value="${product.id}">${product.code} - ${product.name}</option>`);
                    });
                }
            });
        });

        $('#openGAModal').on('click', function () {
            $('#gaModal').modal('show');
        });

        $('#saveGA').on('click', function () {
            let formData = $('#gaForm').serialize();

            $.ajax({
                url: '{{ route('stock_ga.store') }}',
                type: 'POST',
                data: formData,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    alert(response.message); // Show the success message
                    if (response.redirect) {
                        window.location.href = response.redirect; // Redirect to the provided URL
                    }
                },
                error: function (xhr) {
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        alert(xhr.responseJSON.message); // Show the error message
                    } else {
                        alert('An error occurred. Please try again.'); // Fallback error message
                    }
                }
            });
        });

        $('#saveStock').on('click', function () {
            if (!stockIdToUpdate) {
                alert('No stock selected for updating.');
                return;
            }

            const additionalStock = $('#additionalQty').val();

            if (!additionalStock || isNaN(additionalStock) || additionalStock <= 0) {
                alert('Please enter a valid quantity.');
                return;
            }

            $.ajax({
                url: `/stock_ga/addStock/${stockIdToUpdate}`,
                type: 'PATCH',
                data: {
                    additional_stock: additionalStock,
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                },
                success: function (response) {
                    alert(response.message);
                    $('#addStockModal').modal('hide'); // Hide the modal
                    location.reload(); // Reload the page to reflect updated stock
                },
                error: function (xhr) {
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        alert(xhr.responseJSON.message);
                    } else {
                        alert('Failed to update stock. Please try again.');
                    }
                },
            });
        });
    });
</script>
@endsection