@extends('layouts.app')

@section('content')
@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                @endforeach
            </ul>
    </div>
@endif

@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

<div class="block">
    <div class="block-header block-header-default">
        <h3 class="block-title">Input Jurnal</h3>
        <hr>
        <br>
    </div>
    <div class="block-content">
        <form action="{{ route('penjualan.store') }}" method="POST" class="row g-3">
            <div class="form-group row">
                <label class="col-md-2 col-form-label text-right" for="contact_person">Cust :</label>
                <div class="col-md-5">
                    <select name="customer_dom" id="customer_dom" class="form-control select2" required style="width: 100%;">
                        <option value="">Pilih Kota Domisili</option>
                    </select>
                </div>
                <div class="col-md-5">
                    <select name="customer_non_dom" id="customer_non_dom" class="form-control select2" required style="width: 100%;">
                        <option value="">Pilih Kota Luar Domisili</option>
                    </select>
                </div>
            </div>

            <!-- Card with header always visible -->
            
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Initialize Select2 for all dropdowns
    $('.select2').select2();

    // Function to fetch and populate customer DOM data
    function loadCustomerDOM() {
        $.ajax({
            url: "{{ route('penjualan.checkCustomerDOM') }}",
            type: "GET",
            dataType: "json",
            success: function(data) {
                let customerDomSelect = $('#customer_dom');
                customerDomSelect.empty();
                customerDomSelect.append('<option value="">Pilih Kota Domisili</option>');
                $.each(data, function(index, customer) {
                    customerDomSelect.append(
                        `<option value="${customer.customer_id}">
                            ${customer.customer_nama} - ${customer.customer_kota}, ${customer.customer_provinsi}
                        </option>`
                    );
                });
            },
            error: function(xhr) {
                console.error("Failed to fetch customer DOM data", xhr);
            }
        });
    }

    // Function to fetch and populate customer OUTDOM data
    function loadCustomerOUTDOM() {
        $.ajax({
            url: "{{ route('penjualan.checkCustomerOUTDOM') }}",
            type: "GET",
            dataType: "json",
            success: function(data) {
                let customerOutDomSelect = $('#customer_non_dom');
                customerOutDomSelect.empty();
                customerOutDomSelect.append('<option value="">Pilih Kota Luar Domisili</option>');
                $.each(data, function(index, customer) {
                    customerOutDomSelect.append(
                        `<option value="${customer.customer_id}">
                            ${customer.customer_nama} - ${customer.customer_kota}, ${customer.customer_provinsi}
                        </option>`
                    );
                });
            },
            error: function(xhr) {
                console.error("Failed to fetch customer OUTDOM data", xhr);
            }
        });
    }

    // Load the customers when the page loads
    loadCustomerDOM();
    loadCustomerOUTDOM();
});
</script>
@endsection
