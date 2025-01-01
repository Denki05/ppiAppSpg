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
        <h2 class="block-title">Jurnal Senses</h2>
    </div>
    <div class="block-content">
        <form action="{{ route('penjualan.store') }}" method="POST" class="row g-3">
        @csrf
            <input type="hidden" value="Senses" name="brand_name" id="brand_name">

            <!-- Customer selection section -->
            <div class="form-group row" id="customerForm">
                <label class="col-12 col-md-2 col-form-label text-right" for="contact_person">Customer :</label>
                <div class="col-12 col-md-5 mb-2 mb-md-0">
                    <select name="customer_dom" id="customer_dom" class="form-control select2" style="width: 100%;">
                        <option value="">Pilih Kota Domisili</option>
                    </select>
                </div>
                <div class="col-12 col-md-5">
                    <select name="customer_non_dom" id="customer_non_dom" class="form-control select2" style="width: 100%;">
                        <option value="">Pilih Kota Luar Domisili</option>
                    </select>
                </div>
            </div>

            <!-- Cash option -->
            <div class="form-group row">
                <label class="col-12 col-md-2 col-form-label text-right" for="flexCheckDefault">Cash :</label>
                <div class="col-12 col-md-10 d-flex align-items-center">
                    <input class="form-check-input" type="checkbox" value="1" id="customerCash" name="customerCash" style="border-color: #80bdff; box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);">
                </div>
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
                            <tbody></tbody>
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
                                    <th style="min-width: 60px;">Pcs / Botol</th>
                                    <th style="min-width: 40px;">Action</th>
                                </tr>
                            </thead>
                            <tbody><!-- Dynamic rows will be added here --></tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="mt-4">
                <a class="btn btn-danger" href="{{ route('home') }}" role="button">Back</a>
                <button type="submit" class="btn btn-primary">Save</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal for adding Give Away item -->
<div class="modal fade" id="gaModal" tabindex="-1" aria-labelledby="gaModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
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

<!-- Modal for adding Transaksi item -->
<div class="modal fade" id="transaksiModal" tabindex="-1" aria-labelledby="transaksiModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
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

    function loadCustomerData(type) {
        let url = type === 'DOM' ? "{{ route('penjualan.checkCustomerDOM') }}" : "{{ route('penjualan.checkCustomerOUTDOM') }}";
        $.ajax({
            url: url,
            type: "GET",
            dataType: "json",
            success: function (response) {
                let selectElement = type === 'DOM' ? $('#customer_dom') : $('#customer_non_dom');
                selectElement.empty();
                selectElement.append(`<option value="">${response.placeholder}</option>`);

                response.customers.forEach(function (customer) {
                    selectElement.append(`<option value="${customer.customer_id}">${customer.customer_nama} - ${customer.customer_kota}, ${customer.customer_provinsi}</option>`);
                });
            },
            error: function (xhr) {
                console.error(`Failed to fetch customer ${type} data`, xhr);
            }
        });
    }

    // Load customer data on page load
    loadCustomerData('DOM');
    loadCustomerData('OUTDOM');

    // Checkbox visibility handling
    $('#customerCash').on('change', function () {
        let isChecked = $(this).is(':checked');
        $('#customerForm').toggle(!isChecked); // Show/hide customer dropdowns
        $('#customer_dom, #customer_non_dom').prop('disabled', isChecked); // Disable if checked
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

    // Open modal to add a new transaksi item
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
            $('#transaksiVariantSelect').append(`<option value="${product.id}">${product.code} - ${product.name}</option>`);
        });
    });

    // Save transaksi item to the table
    $('#saveTransaksi').on('click', function () {
        let variant = $('#transaksiVariantSelect').val(); // Get selected variant ID
        let variantText = $('#transaksiVariantSelect option:selected').text(); // Get selected variant text
        let qty = $('#transaksiQtyInput').val(); // Get entered quantity

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
});
</script>
@endsection