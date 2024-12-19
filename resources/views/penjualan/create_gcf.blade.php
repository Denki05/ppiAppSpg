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
        <h2 class="block-title">Jurnal GCF</h2>
    </div>
    <br><br>
    <div class="block-content">
        <form action="{{ route('penjualan.store') }}" method="POST" class="row g-3">
        @csrf
            <input type="hidden" value="GCF" name="brand_name" id="brand_name">

            <!-- Customer selection section -->
            <div class="form-group row" id="customerForm">
                <label class="col-12 col-md-2 col-form-label text-right" for="contact_person">Customer :</label>
                <div class="col-12 col-md-5 mb-2 mb-md-0">
                    <select name="customer_dom" id="customer_dom" class="form-control select2"  style="width: 100%;">
                        <option value="">Pilih Kota Domisili</option>
                    </select>
                </div>
                <div class="col-12 col-md-5">
                    <select name="customer_non_dom" id="customer_non_dom" class="form-control select2"  style="width: 100%;">
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
                    <button type="button" class="btn btn-primary row-add" id="row_add"><i class="fa fa-plus"></i> Tambah</button>
                </div>
                <div class="col-md-10">
                    <div class="table-responsive">
                        <table id="datatable_ga" class="table table-striped mt-3">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Variant</th>
                                    <th>Qty</th>
                                    <th>Action</th>
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
                    <button type="button" class="btn btn-success addRow"><i class="fa fa-plus"></i> Tambah</button>
                </div>
                <div class="col-md-12">
                    <div class="table-responsive">
                        <table id="datatable_transaksi" class="table table-striped mt-3">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Variant</th>
                                    <th>Qty</th>
                                    <th>Action</th>
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
@endsection

@section('scripts')
<script type="text/javascript">
$(document).ready(function () {
    // Initialize Select2 globally once
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

    // Initialize DataTable for GA section
    var gaTable = $('#datatable_ga').DataTable({
        paging: false,
        bInfo: false,
        searching: false,
        ordering: false,
        order: [[0, 'desc']],
    });

    // Handle row_add button for GA section
    $('#row_add').on('click', function () {
        if (product_data.length === 0) {
            alert('Data produk belum tersedia. Harap tunggu sebentar.');
            return;
        }

        var newRow = gaTable.row.add([
            '', // Counter
            `<select class="form-control variant_select" name="variant[]">
                <option value="">Pilih Variant</option>
                ${product_data.map(function (product) {
                    return `<option value="${product.id}">${product.code} - ${product.name}</option>`;
                }).join('')}
            </select>`,
            `<input type="number" class="form-control qty_input" name="qty[]" min="1" value="500">`,
            '<button class="btn btn-danger btn-sm delete-row">Delete</button>'
        ]).draw().node();

        // Add counter value (row index) dynamically
        $(newRow).find('td:first').text(gaTable.rows().count());

        // Initialize select2 for the newly added variant dropdown
        $(newRow).find('.variant_select').select2({
            width: '100%',
        });
    });

    // Delete row functionality for GA table
    $(document).on('click', '.delete-row', function () {
        gaTable.row($(this).closest('tr')).remove().draw();
    });

    // Initialize DataTable for Transaksi section
    var transaksiTable = $('#datatable_transaksi').DataTable({
        paging: false,
        bInfo: false,
        searching: false,
        ordering: false,
        order: [[0, 'desc']],
    });

    // Handle addRow button for Transaksi section
    $('.addRow').on('click', function () {
        if (product_data.length === 0) {
            alert('Data produk belum tersedia. Harap tunggu sebentar.');
            return;
        }

        var newRow = transaksiTable.row.add([
            '', // Counter
            `<select class="form-control transaksi_select" name="transaksi[]">
                <option value="">Pilih Variant</option>
                ${product_data.map(function (product) {
                    return `<option value="${product.id}">${product.code} - ${product.name}</option>`;
                }).join('')}
            </select>`,
            `<input type="number" class="form-control qty_input" name="transaksi_qty[]" min="1" value="500">`,
            '<button class="btn btn-danger btn-sm delete-row">Delete</button>'
        ]).draw().node();

        // Add counter value (row index) dynamically
        $(newRow).find('td:first').text(transaksiTable.rows().count());

        // Initialize select2 for the newly added variant dropdown
        $(newRow).find('.transaksi_select').select2({
            width: '100%',
        });
    });

    // Delete row functionality for Transaksi table
    $(document).on('click', '.delete-row', function () {
        transaksiTable.row($(this).closest('tr')).remove().draw();
    });
});
</script>
@endsection