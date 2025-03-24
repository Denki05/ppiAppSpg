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
        <h2 class="block-title">Edit Jurnal Settel #{{ $sales->kode }}</h2>
    </div>
    <div class="block-content">
        <form action="{{ route('penjualan.update_settel', $sales->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-group row">
                <label class="col-12 col-md-2 col-form-label text-right">Customer :</label>
                <div class="col-12 col-md-5">
                    @if($sales->type == 0)
                        <select name="customer" class="form-control select2" required>
                            <option value="">Pilih Customer</option>
                            @foreach($customers as $cust)
                                <option value="{{ $cust->id }}" {{ $cust->id == $sales->customer_id ? 'selected' : '' }}>
                                    {{ $cust->nama }}
                                </option>
                            @endforeach
                        </select>
                    @else
                        <input type="text" class="form-control" value="Cash" readonly>
                    @endif
                </div>
            </div>

            <br>

            <div class="form-group row">
                <label class="col-12 col-md-2 col-form-label text-right" for="flexCheckDefault">Note :</label>
                <div class="col-md-2">
                    <button type="button" class="btn btn-success" id="openTransaksiSisipan"><i class="fa fa-plus"></i></button>
                </div>
            </div>

            <hr>

            <div class="form-group row">
                <h4>Give Away :</h4>
                <div class="col-md-2">
                    <button type="button" class="btn btn-primary" id="openGAModal"><i class="fa fa-plus"></i> Tambah</button>
                </div>
                <div class="col-md-12">
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
                                    $productData = $ga->getProductDataFromApi($ga->product_packaging_id );
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
                <a class="btn btn-danger" href="{{ route('penjualan.settle') }}" role="button">Back</a>
                <button type="submit" class="btn btn-primary">Save</button>
            </div>

            <!-- form note -->
            <div class="modal fade" id="transaksiSisipanModal" tabindex="-1" aria-labelledby="transaksiSisipanLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="transaksiSisipanLabel">Tambah Transaksi Sisipan</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form id="transaksiSisipanForm">
                                <div class="mb-3">
                                    <label for="spg_name_sisipan" class="form-label">SPG</label>
                                    <select class="form-control select2" id="spg_name_sisipan" name="spg_name">
                                        <option value="">Pilih SPG</option>
                                        @foreach($spgUsers as $key)
                                        <option value="{{ $key->id }}" {{ $key->id == $sales->created_by ? 'selected' : '' }}>{{ $key->name }} / {{ $key->email }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="tanggal_jurnal_sisipan" class="form-label">Tanggal</label>
                                    <input type="date" class="form-control" id="tanggal_jurnal_sisipan" name="tanggal_jurnal">
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modal GA -->
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

<!-- Modal item transaksi -->
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
<script>
    $(document).ready(function () {
        // Open modal transaksi sisipan
        $('#openTransaksiSisipan').on('click', function () {
            $('#transaksiSisipanModal').modal('show');

            // Initialize select2
            $('#spg_name_sisipan').select2({
                dropdownParent: $('#transaksiSisipanModal'),
                width: '100%',
            });
        });

        // Inisialisasi DataTable untuk Give Away
        var gaTable = $('#datatable_ga').DataTable({
            paging: false,
            info: false,
            searching: false,
            ordering: false,
        });

        $('#openGAModal').click(function () {
            $('#gaModal').modal('show');
            $('#variantSelect').select2({ dropdownParent: $('#gaModal'), width: '100%' });
        });

        $('#saveGA').click(function () {
            let variant = $('#variantSelect').val();
            let qty = $('#qtyInput').val();
            if (!variant || !qty) return alert('Isi semua data.');

            gaTable.row.add([
                `<input type="hidden" name="variant[]" value="${variant}">${$('#variantSelect option:selected').text()}`,
                `<input type="number" name="qty[]" class="form-control" value="${qty}" min="1">`,
                `<button class="btn btn-danger delete-row"><i class="fa fa-trash"></i></button>`
            ]).draw();

            $('#gaModal').modal('hide');
        });

        $(document).on('click', '.delete-row', function () {
            gaTable.row($(this).closest('tr')).remove().draw();
        });

        var product_data = @json($products);

        // Inisialisasi DataTable untuk Transaksi
        var transaksiTable = $('#datatable_transaksi').DataTable({
            paging: false,
            info: false,
            searching: false,
            ordering: false,
        });

        $('#openTransaksiModal').on('click', function () {
            $('#transaksiModal').modal('show');

            // Pastikan dropdown kosong sebelum mengisi ulang
            $('#transaksiVariantSelect').empty().append('<option value="">Pilih Variant</option>');

            if (typeof product_data !== 'undefined' && product_data.length > 0) {
                product_data.forEach(function (product) {
                    $('#transaksiVariantSelect').append(
                        `<option value="${product.id}">${product.code} - ${product.name}</option>`
                    );
                });
            } else {
                alert('Data produk tidak tersedia.');
            }

            // Inisialisasi Select2
            $('#transaksiVariantSelect').select2({
                dropdownParent: $('#transaksiModal'),
                width: '100%',
            });
        });

        $('#saveTransaksi').on('click', function () {
            let variant = $('#transaksiVariantSelect').val();
            let variantText = $('#transaksiVariantSelect option:selected').text();
            let qty = $('#transaksiQtyInput').val();

            if (!variant || !qty) {
                alert('Harap isi semua data sebelum menyimpan.');
                return;
            }

            transaksiTable.row.add([
                `<input type="hidden" name="transaksi[]" value="${variant}">${variantText}`,
                `<input type="hidden" name="transaksi_qty[]" value="${qty}">${qty}`,
                `<button class="btn btn-danger btn-sm delete-row"><i class="fa fa-trash"></i></button>`,
            ]).draw();

            $('#transaksiForm')[0].reset();
            $('#transaksiModal').modal('hide');
        });

        $(document).on('click', '.delete-row', function () {
            transaksiTable.row($(this).closest('tr')).remove().draw();
        });
    });
</script>
@endsection