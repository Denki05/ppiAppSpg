@extends('layouts.app')

@section('content')
@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="block">
    <div class="block-header block-header-default">
        <h2 class="block-title">Edit Jurnal #{{ $sales->kode }}</h2>
    </div>
    <div class="block-content">
        <form action="{{ route('penjualan.update', $sales->id) }}" method="POST">
            @csrf
            @method('PUT')
            <input type="hidden" name="brand_name" id="brand_name" value="{{ $sales->brand_name }}">

            <!-- Customer selection section -->
            <div class="form-group row" id="customerForm">
                @if($sales->type == 0)
                    <label class="col-12 col-md-2 col-form-label text-right" for="contact_person">Customer :</label>
                    <div class="col-12 col-md-5 mb-2 mb-md-0">
                        <select name="customer" id="customer" class="form-control select2" style="width: 100%;">
                            <option value="">Pilih Customer</option>
                            @foreach($customer as $cust)
                                <option value="{{ $cust->id }}" {{ $cust->id == $sales->customer_id ? 'selected' : '' }}>{{ $cust->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                @else
                    <label class="col-12 col-md-2 col-form-label text-right" for="contact_person">Cash :</label>
                    <div class="col-12 col-md-5 mb-2 mb-md-0">
                        <input type="text" class="form-control" value="Cash" readonly>
                    </div>
                @endif
            </div>
            <hr>

            <!-- Give Away Section -->
            <div class="form-group row">
                <h4>Give Away :</h4><br>
                <div class="col-md-2">
                    <button type="button" class="btn btn-primary" id="openGAModal"><i class="fa fa-plus"></i> Tambah</button>
                </div>
                <div class="col-md-12">
                    <div class="table-responsive">
                        <table id="datatable_ga" class="table table-striped mt-3">
                            <thead>
                                <tr>
                                    <th style="min-width: 150px;">Variant</th>
                                    <th style="min-width: 60px;">Pcs / Botol</th>
                                    <th style="min-width: 40px;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($sales_ga as $ga)
                                    @php
                                        // Fetch product data
                                        $productData = $ga->getProductDataFromApi($ga->product_packaging_id);
                                    @endphp
                                    <tr>
                                        <td>
                                            <input type="hidden" name="variant[]" value="{{ $ga->product_packaging_id }}">
                                            {{ $productData['code'] ?? 'Unknown Product' }} - {{ $productData['name'] ?? 'Unknown Product' }}
                                        </td>
                                        <td>
                                            <input type="number" name="qty[]" class="form-control" value="{{ $ga->pcs }}" min="1">
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-danger delete-row"><i class="fa fa-trash"></i></button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <hr>

            <!-- Transaksi Section -->
            <div class="form-group row">
                <h4>Transaksi :</h4><br>
                <div class="col-md-2">
                    <button type="button" class="btn btn-success" id="openTransaksiModal"><i class="fa fa-plus"></i> Tambah</button>
                </div>
                <div class="col-md-12">
                    <div class="table-responsive">
                        <table id="datatable_transaksi" class="table table-striped mt-3">
                            <thead>
                                <tr>
                                    <th style="min-width: 150px;">Variant</th>
                                    <th style="min-width: 60px;">Qty</th>
                                    <th style="min-width: 40px;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($sales_items as $transaksi)
                                    @php
                                        // Fetch product data
                                        $productData = $transaksi->getProductDataFromApi($transaksi->product_id);
                                    @endphp
                                    <tr>
                                        <td>
                                            <input type="hidden" name="transaksi[]" value="{{ $transaksi->product_id }}">
                                            {{ $productData['code'] ?? 'Unknown Product' }} - {{ $productData['name'] ?? 'Unknown Product' }}
                                        </td>
                                        <td>
                                            <input type="number" name="transaksi_qty[]" class="form-control" value="{{ $transaksi->qty }}" min="1">
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-danger delete-row"><i class="fa fa-trash"></i></button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="mt-4">
                <a class="btn btn-danger" href="{{ route('penjualan.review') }}" role="button">Back</a>
                <button type="submit" class="btn btn-primary">Save</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal for adding Give Away item -->
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
                        <label for="variantSelect" class="form-label">Variant</label>
                        <select class="form-control select2" id="variantSelect" name="variant">
                            <option value="">Pilih Variant</option>
                            @foreach ($stock_ga as $item)
                                <option value="{{ $item['product_id'] }}">
                                    {{ $item['code'] }} - {{ $item['name'] }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="qtyInput" class="form-label">Pcs / Botol</label>
                        <input type="number" class="form-control" id="qtyInput" name="qty" min="1" placeholder="Masukkan jumlah pcs / botol">
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

<div class="modal fade" id="transaksiModal" tabindex="-1" aria-labelledby="transaksiModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="transaksiModalLabel">Tambah Item Transaksi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="transaksiForm">
                    <div class="mb-3">
                        <label for="transaksiVariantSelect" class="form-label">Variant</label>
                        <select class="form-control select2" id="transaksiVariantSelect" name="transaksi_variant">
                            <option value="">Pilih Variant</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="transaksiQtyInput" class="form-label">Qty</label>
                        <input type="number" class="form-control" id="transaksiQtyInput" name="transaksi_qty" min="1" placeholder="Masukkan Qty">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="saveTransaksi">Simpan</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script type="text/javascript">
    $(document).ready(function () {
    $('.select2').select2({
        width: '100%',
    });

    var product_data = []; // Declare product_data globally

    // Preload all product data for the brand when the page loads
    function preloadProducts(brand) {
        return $.ajax({
            url: `/product/brand/${brand}`,
            type: "GET",
            success: function (response) {
                product_data = response; // Store product data globally
            },
            error: function (error) {
                console.error('Failed to preload products:', error);
            }
        });
    }

    // Preload product data for the specified brand
    let brand_name = $('#brand_name').val();
    preloadProducts(brand_name);

    var gaTable = $('#datatable_ga').DataTable({
        paging: false,
        bInfo: false,
        searching: false,
        ordering: false,
    });

    // Hide the table initially if there is no data
    const updateTableVisibility = () => {
        if (gaTable.rows().count() === 0) {
            $('#datatable_ga').closest('.table-responsive').hide();
        } else {
            $('#datatable_ga').closest('.table-responsive').show();
        }
    };

    // Initial table visibility check
    updateTableVisibility();

    // Open modal to add a new give away item
    $('#openGAModal').on('click', function () {
        $('#gaModal').modal('show');

        // Initialize select2 for variant dropdown inside the modal
        $('#variantSelect').select2({
            dropdownParent: $('#gaModal'), // Ensure dropdown appears within modal
            width: '100%',
        });
    });

    // Save give away item to the table
    $('#saveGA').on('click', function () {
        const variant = $('#variantSelect').val(); // Get selected variant ID
        const variantText = $('#variantSelect option:selected').text(); // Get selected variant text
        const qty = $('#qtyInput').val(); // Get entered quantity

        // Validation: Ensure fields are not empty
        if (!variant || !qty) {
            alert('Harap isi semua data sebelum menyimpan.');
            return;
        }

        // Add new row to DataTable
        gaTable.row.add([
            `<input type="hidden" name="variant[]" value="${variant}">${variantText}`,
            `<input type="hidden" name="qty[]" value="${qty}">${qty}`,
            `<button class="btn btn-danger btn-sm delete-row"><i class="fa fa-trash"></i></button>`,
        ]).draw();

        // Update table visibility
        updateTableVisibility();

        // Reset form and close modal
        $('#gaForm')[0].reset();
        $('#gaModal').modal('hide');
    });

    // Delete row functionality
    $(document).on('click', '.delete-row', function () {
        gaTable.row($(this).closest('tr')).remove().draw();

        // Update table visibility after deleting a row
        updateTableVisibility();
    });

    // Initialize DataTable for Transaksi section
    // var transaksiTable = $('#datatable_transaksi').DataTable({
    //     paging: false,
    //     bInfo: false,
    //     searching: false,
    //     ordering: false,
    // });

    // Initialize DataTable for Transaksi section
    var transaksiTable = $('#datatable_transaksi').DataTable({
        paging: false,
        bInfo: false,
        searching: false,
        ordering: false,
    });

    // Hide the table initially if there is no data
    const updateTableVisibilityTransaksi = () => {
        if (transaksiTable.rows().count() === 0) {
            $('#datatable_transaksi').closest('.table-responsive').hide();
        } else {
            $('#datatable_transaksi').closest('.table-responsive').show();
        }
    };

    // Initial table visibility check
    updateTableVisibilityTransaksi();

    $('#openTransaksiModal').on('click', function () {
        $('#transaksiModal').modal('show');

        // Initialize select2 for variant dropdown inside the modal
        $('#transaksiVariantSelect').select2({
            dropdownParent: $('#transaksiModal'), // Ensure dropdown appears within modal
            width: '100%',
        });

        // Populate the variant dropdown with preloaded product data
        $('#transaksiVariantSelect').empty();
        $('#transaksiVariantSelect').append('<option value="">Pilih Variant</option>');
        product_data.forEach(function (product) {
            $('#transaksiVariantSelect').append(`<option value="${product.product_id}">${product.code} - ${product.name}</option>`);
        });
    });

    // Save transaksi item to the table
    $('#saveTransaksi').on('click', function () {
        const variant = $('#transaksiVariantSelect').val(); // Get selected variant ID
        const variantText = $('#transaksiVariantSelect option:selected').text(); // Get selected variant text
        const qty = $('#transaksiQtyInput').val(); // Get entered quantity

        // Validation: Ensure fields are not empty
        if (!variant || !qty) {
            alert('Harap isi semua data sebelum menyimpan.');
            return;
        }

        // Add new row to DataTable
        transaksiTable.row.add([
            `<input type="hidden" name="transaksi[]" value="${variant}">${variantText}`,
            `<input type="hidden" name="transaksi_qty[]" value="${qty}">${qty}`,
            `<button class="btn btn-danger btn-sm delete-row"><i class="fa fa-trash"></i></button>`,
        ]).draw();

        updateTableVisibilityTransaksi();

        // Reset form and close modal
        $('#transaksiForm')[0].reset();
        $('#transaksiModal').modal('hide');
    });

    // Delete row functionality for Transaksi table
    $(document).on('click', '.delete-row', function () {
        transaksiTable.row($(this).closest('tr')).remove().draw();
    });

    // Handle addRow button for Transaksi section
    // $('.addRow').on('click', function () {
    //     if (product_data.length === 0) {
    //         alert('Data produk belum tersedia. Harap tunggu sebentar.');
    //         return;
    //     }

    //     var newRow = transaksiTable.row.add([
    //         `<select class="form-control transaksi_select" name="transaksi[]">
    //             <option value="">Pilih Variant</option>
    //             ${product_data.map(function (product) {
    //                 return `<option value="${product.id}">${product.code} - ${product.name}</option>`;
    //             }).join('')}
    //         </select>`,
    //         `<input type="number" class="form-control qty_input" name="transaksi_qty[]" min="1" value="500">`,
    //         '<button class="btn btn-danger btn-sm delete-row"><i class="fa-solid fa-trash"></i></button>'
    //     ]).draw().node();

    //     // Add counter value (row index) dynamically
    //     // $(newRow).find('td:first').text(transaksiTable.rows().count());

    //     // Initialize select2 for the newly added variant dropdown
    //     $(newRow).find('.transaksi_select').select2({
    //         width: '100%',
    //     });
    // });

    // // Delete row functionality for Transaksi table
    // $(document).on('click', '.delete-row', function () {
    //     transaksiTable.row($(this).closest('tr')).remove().draw();
    // });
});
</script>
@endsection
