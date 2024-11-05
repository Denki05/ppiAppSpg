@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Create Sale</h2>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('penjualan.store') }}" method="POST" class="row g-3">
        @csrf

        <div class="row g-3 mt-4">
            <div class="col-md-6">
                <label for="tanggal_order" class="form-label">Tanggal <span class="text-danger">*</span></label>
                <input type="date" name="tanggal_order" class="form-control" id="tanggal_order" required>
            </div>

            <div class="col-md-6">
                <label for="brand" class="form-label">Brand <span class="text-danger">*</span></label>
                <select name="brand" id="brand" class="form-control select2" required>
                    <option value="">Pilih Brand</option>
                    @foreach($brands as $brand)
                        <option value="{{ $brand['brand_name'] }}">{{ $brand['brand_name'] }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-6">
                <label for="nama_customer" class="form-label">Nama Customer (Toko) <span class="text-danger">*</span></label>
                <input type="text" name="nama_customer" class="form-control" id="nama_customer" required>
            </div>

            <div class="col-md-6">
                <label for="alamat_customer" class="form-label">Alamat Customer (Toko) <span class="text-danger">*</span></label>
                <input type="text" name="alamat_customer" class="form-control" id="alamat_customer" required>
            </div>

            <div class="col-md-6">
                <label for="kontak_person" class="form-label">Owner / Contact Person <span class="text-danger">*</span></label>
                <input type="text" name="kontak_person" class="form-control" id="kontak_person" required>
            </div>

            <div class="col-md-6">
                <label for="telpon" class="form-label">Telephone</label>
                <input type="text" name="telpon" class="form-control" id="telpon">
            </div>
        </div>

        <div class="row g-3 mt-4">
            <hr>
            <div class="col-12">
                <button type="button" class="btn btn-success" id="addRow"><i class="fa fa-plus" aria-hidden="true"></i> Add Row</button>
                <table id="salesTable" class="table table-striped mt-3">
                    <thead>
                        <tr>
                            <th class="text-center">#</th>
                            <th class="text-center">Variant</th>
                            <th class="text-center">Qty</th>
                            <th class="text-center">Unit Weight</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- JavaScript will dynamically add rows here -->
                    </tbody>
                </table>
            </div>
        </div>

        <div class="col-12 mt-4">
            <a class="btn btn-danger" href="{{ route('penjualan.index') }}" role="button">Back</a>
            <button type="submit" class="btn btn-primary">Save</button>
        </div>
    </form>
</div>

@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Initialize Select2
    $('.select2').select2();

    let productOptions = []; // Store product options for the selected brand

    // Load products based on selected brand
    $('#brand').change(function() {
        const brandName = $(this).val();
        const tableBody = $('#salesTable tbody');

        // Clear previous rows when brand is changed
        tableBody.empty();

        if (brandName) {
            $.ajax({
                url: '/product/brand/' + brandName,
                method: 'GET',
                success: function(data) {
                    if (data.length > 0) {
                        // Map product data to options
                        productOptions = data.map(product => `<option value="${product.id}">${product.code} - ${product.name}</option>`).join('');
                    } else {
                        productOptions = []; // Reset if no products found
                    }
                },
                error: function() {
                    alert('Failed to load products.');
                }
            });
        } else {
            productOptions = []; // Reset if no brand selected
        }
    });

    // Add row to sales table
    $('#addRow').click(function() {
        const tableBody = $('#salesTable tbody');
        const rowIndex = tableBody.find('tr').length + 1; // Get current row count

        // Create a new row with the current product options
        const newRow = `
            <tr>
                <td class="text-center">${rowIndex}</td>
                <td class="text-center">
                    <select name="products[${rowIndex}][variant]" class="form-control select2" required>
                        <option value="">Select Product</option>
                        ${productOptions} <!-- Use the current product options -->
                    </select>
                </td>
                <td class="text-center">
                    <input type="number" name="products[${rowIndex}][quantity]" class="form-control" required min="1">
                </td>
                <td class="text-center">
                    <select name="products[${rowIndex}][unit_weight]" class="form-control select2" required>
                        <option value="">-- Select Unit --</option>
                        @foreach($unit_weights as $key => $value)
                            <option value="{{ $key }}">{{ $value }}</option>
                        @endforeach
                    </select>
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-danger removeRow">Remove</button>
                </td>
            </tr>`;
        tableBody.append(newRow);
        $('.select2').select2(); // Reinitialize Select2 for the new dropdowns
    });

    // Remove row from table and re-index
    $(document).on('click', '.removeRow', function() {
        $(this).closest('tr').remove();
        reIndexRows(); // Call re-indexing function
    });

    // Function to re-index rows
    function reIndexRows() {
        $('#salesTable tbody tr').each(function(index) {
            $(this).find('td:first').text(index + 1); // Update the row number
            $(this).find('select[name^="products"]').each(function() {
                const name = $(this).attr('name');
                const newName = name.replace(/products\[\d+\]/, `products[${index + 1}]`);
                $(this).attr('name', newName); // Update the name attribute
            });
        });
    }
});
</script>
@endsection