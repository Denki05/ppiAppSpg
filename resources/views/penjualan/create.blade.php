@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h2>#Input Penjualan</h2>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('penjualan.store') }}" method="POST" class="row g-3">
        @csrf

        <!-- Tabs Navigation -->
        <ul class="nav nav-tabs" id="brandTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <a class="nav-link active" id="gcf-tab" data-bs-toggle="tab" href="#gcf" role="tab" aria-controls="gcf" aria-selected="true" data-brand="GCF">GCF</a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link" id="senses-tab" data-bs-toggle="tab" href="#senses" role="tab" aria-controls="senses" aria-selected="false" data-brand="Senses">SENSES</a>
            </li>
        </ul>

        <!-- Tabs Content -->
        <div class="tab-content" id="brandTabsContent">
            <!-- GCF Tab Content -->
            <div class="tab-pane fade show active" id="gcf" role="tabpanel" aria-labelledby="gcf-tab">
                @include('penjualan.partials.sales_form', ['brand' => 'GCF'])
            </div>

            <!-- Senses Tab Content -->
            <div class="tab-pane fade" id="senses" role="tabpanel" aria-labelledby="senses-tab">
                @include('penjualan.partials.sales_form', ['brand' => 'Senses'])
            </div>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Initialize Select2 for all dropdowns
    $('.select2').select2();

    // Function to load products for a selected brand and populate the select element
    function loadProductsForBrand(brand, selectElement) {
        $.ajax({
            url: '/product/brand/' + brand,
            method: 'GET',
            success: function(data) {
                selectElement.empty().append('<option value="">Select Product</option>');
                data.forEach(function(product) {
                    selectElement.append(`<option value="${product.id}">${product.name}</option>`);
                });
            },
            error: function() {
                alert('Failed to load products for brand: ' + brand);
            }
        });
    }

    // Load products for the active tab on tab switch
    $('#brandTabs a').on('shown.bs.tab', function (e) {
        const brand = $(e.target).data('brand');
        const tableBody = $(`#${brand.toLowerCase()}Table tbody`);
        const selectElement = tableBody.find('select[name^="' + brand.toLowerCase() + 'Products[0][variant]"]');
        loadProductsForBrand(brand, selectElement);
    });

    // Initial load for the active tab
    const activeBrand = $('#brandTabs .nav-link.active').data('brand');
    loadProductsForBrand(activeBrand, $(`#${activeBrand.toLowerCase()}Table tbody select[name^="${activeBrand.toLowerCase()}Products[0][variant]"]`));

    // Adding new product row based on the active tab
    $(document).on('click', '.addRow', function() {
        const brand = $(this).data('brand');
        const tableBody = $(`#${brand.toLowerCase()}Table tbody`);
        const rowIndex = tableBody.find('tr').length;

        // Create a new row with the correct product select
        const newRow = `
            <tr>
                <td class="text-center" style="width: 25%">
                    <select name="${brand.toLowerCase()}Products[${rowIndex}][variant]" class="form-control select2" required>
                        <option value="">Select Product</option>
                        <!-- Options will load based on brand selection -->
                    </select>
                </td>
                <td class="text-center" style="width: 20%">
                    <input type="number" name="${brand.toLowerCase()}Products[${rowIndex}][quantity]" class="form-control" required min="1">
                </td>
                <td class="text-center" style="width: 5%">
                    <button type="button" class="btn btn-danger removeRow">
                        <i class="fa fa-trash"></i>
                    </button>
                </td>
            </tr>`;
        
        tableBody.append(newRow);
        $('.select2').select2(); // Reinitialize Select2 for newly added rows

        // Load products in the newly added select element
        const selectElement = tableBody.find(`select[name="${brand.toLowerCase()}Products[${rowIndex}][variant]"]`);
        loadProductsForBrand(brand, selectElement);
    });

    // Remove a row from the product table
    $(document).on('click', '.removeRow', function() {
        $(this).closest('tr').remove();
    });
});
</script>
@endsection