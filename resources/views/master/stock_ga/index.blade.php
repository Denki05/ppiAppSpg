@extends('layouts.app')

@section('content')
<div class="container">
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
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
            <button type="button" class="btn btn-primary" id="openGAModal"><i class="fa fa-plus"></i> Tambah</button>
            <br><br>
            <table id="variantStock" class="table table-striped">
                <thead>
                    <tr>
                        <th class="text-center">#</th>
                        <th class="text-center">Variant</th>
                        <th class="text-center">Pcs / Botol</th>
                        <th class="text-center">Stock</th>
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
                        <label for="qtyInput" class="form-label">Qty</label>
                        <input type="number" class="form-control" id="qtyInput" name="qty" min="1" placeholder="Masukkan Qty">
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
        $('#brandSelect').select2({
            width: '100%',
            dropdownParent: $('#gaModalLabel'), // Ensures the dropdown stays within the modal
        });

        $('#variantSelect').select2({
            width: '100%',
            dropdownParent: $('#gaModalLabel'), // Ensures the dropdown stays within the modal
        });

        var gaTable = $('#variantStock').DataTable({
            paging: false,
            bInfo: false,
            searching: false,
            ordering: false,
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