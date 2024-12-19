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
                        <th class="text-center">Stock</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($stocks as $stock)
                    <tr>
                        <td class="text-center">{{ $loop->iteration }}</td>
                        <td class="text-center">{{ $stock->product_id }}</td>
                        <td class="text-center">{{ $stock->qty }}</td>
                        <td class="text-center"></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

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
@endsection

@section('scripts')
<script>
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
    });
</script>
@endsection