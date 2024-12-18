@extends('layouts.app')

@section('content')
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

<div class="block">
    <div class="block-header block-header-default">
        <h2 class="block-title">Jurnal Senses</h2>
    </div>
    <br>
    <br>
    <div class="block-content">
        <form action="{{ route('penjualan.store') }}" method="POST" class="row g-3">
        @csrf
            <input type="hidden" value="Senses" name="brand_name" id="brand_name">

            <!-- Customer selection section -->
            <div class="form-group row" id="customerForm">
                <label class="col-12 col-md-2 col-form-label text-right" for="contact_person">Customer :</label>
                <div class="col-12 col-md-5 mb-2 mb-md-0">
                    <select name="customer_dom" id="customer_dom" class="form-control select2" required style="width: 100%;">
                        <option value="">Pilih Kota Domisili</option>
                    </select>
                </div>
                <div class="col-12 col-md-5">
                    <select name="customer_non_dom" id="customer_non_dom" class="form-control select2" required style="width: 100%;">
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
                <h4>Give Away :</h4>
                <br>
                <div class="col-md-2">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal">
                        <i class="fa fa-plus"></i> Tambah
                    </button>
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
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <hr>
            
            <!-- Transaksi Section -->
            <div class="form-group row">
                <h4>Transaksi :</h4>
                <br>
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
                            <tbody>
                                <!-- Dynamic rows will be added here -->
                            </tbody>
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

<!-- Modal GA -->
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Add GA Variant</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="form_variant">
          <div class="row mb-3">
            <label for="variant_dropdown" class="col-12 col-md-3 col-form-label">Variant</label>
            <div class="col-12 col-md-9">
                <select id="variant_dropdown" class="form-control select2" style="width: 100%;">
                    <option value="">Pilih Variant</option>
                </select>
            </div>
          </div>
          <div class="row mb-3">
            <label for="variant_qty" class="col-12 col-md-3 col-form-label">Qty</label>
            <div class="col-12 col-md-9">
              <input type="number" class="form-control" id="variant_qty" name="variant_qty" placeholder="Enter quantity">
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" id="add_variant">Add Variant</button>
      </div>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<script type="text/javascript">
$(document).ready(function () {
    // Initialize Select2 globally
    $('.select2').select2({
        width: '100%',
    });

    $('#exampleModal').on('shown.bs.modal', function () {
        $('#variant_dropdown').select2({
            dropdownParent: $('#exampleModal'),
        });
    });

    // Function to load customer data
    function loadCustomerData(type) {
        let url = type === 'DOM' ? "{{ route('penjualan.checkCustomerDOM') }}" : "{{ route('penjualan.checkCustomerOUTDOM') }}";
        $.ajax({
            url: url,
            type: "GET",
            dataType: "json",
            success: function (data) {
                let selectElement = type === 'DOM' ? $('#customer_dom') : $('#customer_non_dom');
                selectElement.empty().append(`<option value="">Pilih Kota ${type === 'DOM' ? 'Domisili' : 'Luar Domisili'}</option>`);
                data.forEach(function (customer) {
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
        $('#customerForm').toggle(!isChecked);
        $('#customer_dom, #customer_non_dom').prop('disabled', isChecked);
    });
});
</script>
@endsection